<?php

namespace App\Http\Controllers\ForumCommunity;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;

use App\Models\CommunityCategory;
use App\Models\Community;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\DB;

class CommunityCategoryController extends Controller
{

    /**
     * @OA\Get(
     *     path="/community-categories",
     *     summary="Ambil daftar kategori komunitas",
     *     description="Mengambil seluruh kategori komunitas yang tersedia.",
     *     tags={"Forum Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar kategori komunitas berhasil dimuat.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="category_id", type="string", example="categ-uuid-example"),
     *                 @OA\Property(property="name", type="string", example="Hobi"),
     *                 @OA\Property(property="photo", type="string", example="https://api-polinema.webview.cloud/community/komunitas.png"),
     *                 @OA\Property(property="created_dt", type="string", format="date-time", example="2025-04-27T20:01:54")
     *             )
     *         )
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
    public function index()
    {
        $categories = CommunityCategory::all();
        return ApiResponse::success('Daftar komunitas berhasil dimuat.', $categories);
    }

    /**
     * @OA\Post(
     *     path="/community",
     *     tags={"Forum Komunitas"},
     *     summary="Buat komunitas baru",
     *     description="Endpoint ini digunakan untuk membuat komunitas baru dan sekaligus menambahkan user sebagai Ketua Grup. Setiap user hanya diperbolehkan membuat satu komunitas sebagai Ketua Grup.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "category_id"},
     *                 @OA\Property(property="name", type="string", example="Komunitas Laravel Indonesia"),
     *                 @OA\Property(property="description", type="string", example="Tempat belajar Laravel bareng"),
     *                 @OA\Property(property="logo", type="file"),
     *                 @OA\Property(property="category_id", type="string", example="categ-4560aef8-3f5d-42aa-a39a-e8b0a34ea517")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Komunitas berhasil dibuat",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User sudah membuat komunitas sebagai Ketua Grup",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Gagal membuat komunitas",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required|string|exists:community_categories,category_id'
        ], [
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia di system.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah user sudah membuat komunitas sebagai Ketua Grup
        $existingCommunity = $user->communities()
            ->wherePivot('role', 'Ketua Grup')
            ->first();

        if ($existingCommunity) {
            return ApiResponse::error('Maaf, kamu sudah membuat sebuah komunitas.', [
                'existing_community' => $existingCommunity
            ], 403);
        }

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $uploadPath = public_path("community");

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            $logoPath = url("community/{$fileName}");
        }

        DB::beginTransaction();

        try {
            $community = Community::create([
                'name' => $request->name,
                'description' => $request->description,
                'logo' => $logoPath,
                'created_dt' => Carbon::now(),
                'category_id' => $request->category_id
            ]);

            $community->members()->attach($user->user_id, [
                'joined_dt' => Carbon::now(),
                'role' => 'Ketua Grup'
            ]);

            DB::commit();

            return ApiResponse::success('Komunitas berhasil dibuat.', $community, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal membuat komunitas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/community/{communityId}/members/{memberId}",
     *     tags={"Forum Komunitas"},
     *     summary="Kick member dari komunitas",
     *     description="Hanya Ketua Grup yang dapat mengeluarkan member dari komunitas.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="communityId",
     *         in="path",
     *         required=true,
     *         description="ID komunitas",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID user/member yang ingin dikeluarkan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member berhasil dikeluarkan"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Bukan ketua grup"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User bukan anggota komunitas"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Tidak bisa kick diri sendiri"
     *     )
     * )
     */
    public function kickMember($communityId, $memberId)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $isKetua = DB::table('community_user')
            ->where('community_id', $communityId)
            ->where('user_id', $user->user_id)
            ->where('role', 'Ketua Grup')
            ->exists();

        if (!$isKetua) {
            return ApiResponse::error('Akses ditolak. Hanya Ketua Grup yang dapat mengeluarkan member.', [], 403);
        }

        if ($user->user_id === $memberId) {
            return ApiResponse::error('Anda tidak dapat mengeluarkan diri sendiri dari komunitas.', [], 400);
        }

        $isMember = DB::table('community_user')
            ->where('community_id', $communityId)
            ->where('user_id', $memberId)
            ->exists();

        if (!$isMember) {
            return ApiResponse::error('User ini bukan anggota komunitas.', [], 404);
        }

        DB::table('community_user')
            ->where('community_id', $communityId)
            ->where('user_id', $memberId)
            ->delete();

        return ApiResponse::success('Member berhasil dikeluarkan dari komunitas.');
    }

}
