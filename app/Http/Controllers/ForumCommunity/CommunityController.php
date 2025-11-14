<?php

namespace App\Http\Controllers\ForumCommunity;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityUser;
use App\Helpers\ApiResponse;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Http\Request;


class CommunityController extends Controller
{

    /**
     * @OA\Get(
     *     path="/communities",
     *     summary="Ambil daftar komunitas",
     *     description="Mengambil seluruh komunitas beserta jumlah membernya. Jika parameter `category_id` disertakan, hanya komunitas dengan kategori tersebut yang akan ditampilkan. Wajib menggunakan token otentikasi.",
     *     tags={"Forum Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter komunitas berdasarkan category_id. Opsional.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar komunitas berhasil dimuat."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Response type",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default="application/json"
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        $query = Community::withCount('members')->with([
            'members' => function ($q) {
                $q->wherePivot('role', 'Ketua Grup'); 
            }
        ]);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $communities = $query->get();

        $communities->transform(function ($community) use ($user) {
            
            $isJoined = $community->members()
                ->where('community_user.user_id', $user->user_id)
                ->exists();

            
            $creator = $community->members->first();

            return [
                'community_id'   => $community->community_id,
                'name'           => $community->name,
                'description'    => $community->description,
                'logo'           => $community->logo,
                'created_dt'     => $community->created_dt,
                'created_by'     => $creator ? $creator->name : null,
                'members_count'  => $community->members_count,
                'is_joined'      => $isJoined,
            ];
        });

        return ApiResponse::success('Daftar komunitas berhasil dimuat.', $communities);
    }

    /**
     * @OA\Get(
     *     path="/my-communities",
     *     summary="Ambil daftar komunitas yang diikuti oleh user",
     *     description="Mengambil seluruh komunitas yang diikuti oleh user yang sedang login. Dapat difilter berdasarkan kategori dengan parameter category_id.",
     *     tags={"Forum Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="ID kategori untuk memfilter komunitas berdasarkan kategori",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar komunitas yang diikuti berhasil dimuat.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Response type",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default="application/json"
     *         )
     *     )
     * )
     */
    public function myCommunities(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $query = Community::withCount('members')
            ->whereHas('members', function ($query) use ($user) {
                $query->where('community_user.user_id', $user->user_id);
            });

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $communities = $query->get();

        return ApiResponse::success('Daftar komunitas yang diikuti berhasil dimuat.', $communities);
    }

    /**
     * @OA\Get(
     *     path="/communities/{community_id}",
     *     summary="Ambil detail komunitas berdasarkan ID Komunitas",
     *     description="Menampilkan detail komunitas beserta anggota dan jumlah anggota. Wajib menggunakan token otentikasi.",
     *     tags={"Forum Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="community_id",
     *         in="path",
     *         required=true,
     *         description="ID komunitas yang ingin dilihat detailnya",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil detail komunitas"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Komunitas tidak ditemukan"
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Response type",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default="application/json"
     *         )
     *     )
     * )
     */
    public function show($community_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $community = Community::with(['members' => function ($query) {
            $query->select('users.user_id', 'users.name', 'users.photo', 'community_user.joined_dt', 'community_user.role');
        }])
            ->withCount('members')
            ->findOrFail($community_id);

        $community->posts_per_day = rand(1, 10);

        $isJoined = $community->members->contains('user_id', $user->user_id);
        $community->is_joined = $isJoined;

        $ketua = $community->members->first(function ($member) {
            return $member->pivot->role === 'Ketua Grup';
        });

        $community->creator = $ketua;

        return ApiResponse::success('Detail komunitas berhasil dimuat.', $community);
    }

    /**
     * @OA\Post(
     *     path="/communities/join/{community_id}",
     *     summary="Bergabung ke komunitas berdasarkan ID",
     *     description="Mendaftarkan pengguna ke dalam komunitas tertentu. Menggunakan token otentikasi untuk validasi pengguna.",
     *     tags={"Forum Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="community_id",
     *         in="path",
     *         required=true,
     *         description="ID komunitas yang ingin diikuti",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil bergabung ke komunitas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Berhasil bergabung ke komunitas"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Komunitas tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Komunitas tidak ditemukan"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User sudah bergabung ke komunitas ini",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Kamu sudah bergabung ke komunitas ini"
     *             )
     *         )
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Response type",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default="application/json"
     *         )
     *     )
     * )
     */
    public function join($communityId)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return ApiResponse::error('User tidak ditemukan atau token tidak valid', 401);
        }

        $community = Community::find($communityId);

        if (!$community) {
            return ApiResponse::error('Komunitas tidak ditemukan', 404);
        }

        $exists = CommunityUser::where('community_id', $communityId)
            ->where('user_id', $user->user_id)
            ->exists();

        if ($exists) {
            return ApiResponse::success('Mohon maaf kamu sudah bergabung kesini.');
        }

        CommunityUser::create([
            'community_id' => $communityId,
            'user_id' => $user->user_id,
            'joined_dt' => Carbon::now(),
            'role' => 'Anggota'
        ]);

        return ApiResponse::success('Berhasil bergabung ke komunitas');
    }

    /**
     * @OA\Delete(
     *     path="/communities/unjoin/{community_id}",
     *     summary="Keluar dari komunitas berdasarkan ID",
     *     description="Menghapus relasi antara pengguna dan komunitas, sehingga pengguna tidak lagi menjadi anggota komunitas.",
     *     tags={"Forum Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="community_id",
     *         in="path",
     *         required=true,
     *         description="ID komunitas yang ingin ditinggalkan",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil keluar dari komunitas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Berhasil keluar dari komunitas"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Komunitas tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Komunitas tidak ditemukan"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User belum bergabung dengan komunitas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Kamu belum bergabung dengan komunitas ini"
     *             )
     *         )
     *     ),
     *     @OA\Header(
     *         header="Accept",
     *         description="Response type",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default="application/json"
     *         )
     *     )
     * )
     */
    public function unjoin($communityId)
    {
        // Ambil user berdasarkan token
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return ApiResponse::error('User tidak ditemukan atau token tidak valid', [], 401);
        }

        // Cari komunitas berdasarkan ID
        $community = Community::find($communityId);

        if (!$community) {
            return ApiResponse::error('Komunitas tidak ditemukan', 404);
        }

        // Periksa apakah user sudah bergabung dengan komunitas ini
        $exists = CommunityUser::where('community_id', $communityId)
            ->where('user_id', $user->user_id)
            ->exists();

        if (!$exists) {
            return ApiResponse::error('Kamu belum bergabung dengan komunitas ini', 400);
        }

        // Hapus relasi user dengan komunitas
        CommunityUser::where('community_id', $communityId)
            ->where('user_id', $user->user_id)
            ->delete();

        return ApiResponse::success('Berhasil keluar dari komunitas');
    }
}
