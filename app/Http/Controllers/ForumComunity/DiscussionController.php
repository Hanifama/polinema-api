<?php

namespace App\Http\Controllers\ForumComunity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discussion;
use App\Helpers\ApiResponse;
use App\Models\CommunityUser;
use App\Models\DiscussionComment;
use App\Models\DiscussionCommentLike;
use App\Models\DiscussionLike;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DiscussionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/discussions",
     *     summary="Lihat semua diskusi",
     *     description="Mengambil daftar semua diskusi yang ada, diurutkan berdasarkan tanggal pembuatan terbaru.",
     *     tags={"Post Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar diskusi berhasil dimuat",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Terjadi kesalahan server"
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
    public function all()
    {
        $discussions = Discussion::orderBy('created_dt', 'desc')->get();
        return ApiResponse::success('Semua diskusi dimuat.', $discussions);
    }

    /**
     * @OA\Get(
     *     path="/community/discussions/{community_id}",
     *     summary="Lihat detail diskusi berdasarkan ID",
     *     description="Mengambil detail diskusi berdasarkan ID komunitas yang diberikan, diurutkan berdasarkan tanggal pembuatan terbaru.",
     *     tags={"Post Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="community_id",
     *         in="path",
     *         required=true,
     *         description="ID dari diskusi yang ingin dilihat",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diskusi komunitas berhasil ditemukan",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan"
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
    public function index($community_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $discussions = Discussion::with(['user:user_id,name,photo'])
            ->withCount(['comments as comment_cnt'])
            ->where('community_id', $community_id)
            ->orderBy('created_dt', 'desc')
            ->get();

        $ids = $discussions->pluck('discus_id')->toArray();

        $likedIds = [];
        if (!empty($ids)) {
            $likedIds = DiscussionLike::whereIn('discus_id', $ids)
                ->where('user_id', $user->user_id)
                ->pluck('discus_id')
                ->toArray();
        }

        $discussions->transform(function ($discussion) use ($likedIds) {
            $discussion->is_like = in_array($discussion->discus_id, $likedIds);
            return $discussion;
        });

        return ApiResponse::success('Diskusi komunitas ditemukan.', $discussions);
    }

    /**
     * @OA\Post(
     *     path="/community/discussion/{community_id}",
     *     summary="Buat diskusi baru dalam komunitas",
     *     description="Membuat diskusi baru dalam komunitas berdasarkan ID komunitas yang diberikan, dengan opsional untuk mengunggah lampiran.",
     *     tags={"Post Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="community_id",
     *         in="path",
     *         required=true,
     *         description="ID komunitas tempat diskusi akan dibuat",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data untuk membuat diskusi baru",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "content"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="Judul Diskusi",
     *                     description="Judul diskusi yang akan dibuat"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="Ini adalah konten diskusi.",
     *                     description="Konten diskusi"
     *                 ),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="string",
     *                     format="binary",
     *                     description="File lampiran (opsional) untuk diskusi. Mendukung jpg, jpeg, png, pdf, doc, docx, dan mp4."
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Diskusi berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="discus_id", type="string", example="12345"),
     *             @OA\Property(property="community_id", type="string", example="67890"),
     *             @OA\Property(property="title", type="string", example="Judul Diskusi"),
     *             @OA\Property(property="content", type="string", example="Ini adalah konten diskusi."),
     *             @OA\Property(property="view_cnt", type="integer", example=0),
     *             @OA\Property(property="like_cnt", type="integer", example=0),
     *             @OA\Property(property="comment_cnt", type="integer", example=0),
     *             @OA\Property(property="attachment_url", type="string", example="http://example.com/diskusi/attachment.jpg"),
     *             @OA\Property(property="attachment_mime", type="string", example="image/jpeg"),
     *             @OA\Property(property="created_dt", type="string", example="2025-04-20T12:34:56")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Judul dan Konten harus diisi.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Komunitas tidak ditemukan"),
     * )
     */
    public function store(Request $request, $community_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4|max:10240',
        ]);

        $discussion = new Discussion([
            'community_id' => $community_id,
            'user_id' => $user->user_id,
            'title' => $request->title,
            'content' => $request->content,
            'view_cnt' => 0,
            'like_cnt' => 0,
            'comment_cnt' => 0,
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('discussion');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            $discussion->attachment = url('discussion/' . $filename);
            $discussion->attachment_mime = $file->getClientMimeType();
        }

        $discussion->save();

        $responseData = [
            'discus_id' => $discussion->discus_id,
            'community_id' => $discussion->community_id,
            'title' => $discussion->title,
            'content' => $discussion->content,
            'view_cnt' => $discussion->view_cnt,
            'like_cnt' => $discussion->like_cnt,
            'comment_cnt' => $discussion->comment_cnt,
            'attachment_url' => $discussion->attachment,
            'attachment_mime' => $discussion->attachment_mime,
            'created_dt' => $discussion->created_dt,

        ];

        return ApiResponse::success('Diskusi berhasil dibuat!', $responseData, 201);
    }

    /**
     * @OA\Get(
     *     path="/discussions/{discus_id}",
     *     summary="Lihat detail diskusi",
     *     description="Menampilkan detail diskusi berdasarkan ID diskusi.",
     *     tags={"Post Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID diskusi yang ingin dilihat",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diskusi ditemukan",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diskusi tidak ditemukan.")
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
    public function show($discus_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $discussions = Discussion::with(['user:user_id,name,photo'])
            ->withCount(['comments as comment_cnt'])
            ->where('discus_id', $discus_id)
            ->orderBy('created_dt', 'desc')
            ->get();

        if ($discussions->isEmpty()) {
            return ApiResponse::error('Diskusi tidak ditemukan.', [], 404);
        }

        $discus_id = $discussions->first()->discus_id;

        $likedIds = DiscussionLike::where('discus_id', $discus_id)
            ->where('user_id', $user->user_id)
            ->exists();

        $discussions->transform(function ($discussion) use ($likedIds) {
            $discussion->is_like = $likedIds;
            return $discussion;
        });

        return ApiResponse::success('Diskusi ditemukan.', $discussions);
    }

    /**
     * @OA\Delete(
     *     path="/discussions/{discus_id}",
     *     summary="Hapus diskusi",
     *     description="Menghapus diskusi berdasarkan ID diskusi.",
     *     tags={"Post Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID diskusi yang ingin dihapus",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diskusi berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diskusi berhasil dihapus.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan",
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
    public function destroy($discus_id)
    {
        Discussion::where('discus_id', $discus_id)->delete();

        return ApiResponse::success('Diskusi berhasil dihapus.');
    }

    /**
     * @OA\Delete(
     *     path="/discussions/discus/role/{discus_id}",
     *     summary="Hapus diskusi",
     *     description="Menghapus diskusi hanya untuk role nya ketua grup berdasarkan ID diskusi.",
     *     tags={"Post Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID diskusi yang ingin dihapus",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Diskusi berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diskusi berhasil dihapus.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Bukan Ketua Grup.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan",
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
    public function deleteDiscussion($discus_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $discus_id = (string) $discus_id;


        if (trim($discus_id) === '') {
            return ApiResponse::error('ID diskusi tidak valid.', [], 400);
        }

        $discussion = Discussion::where('discus_id', $discus_id)->first();
        if (!$discussion) {
            return ApiResponse::error('Diskusi tidak ditemukan.', [], 404);
        }

        $isKetua = CommunityUser::where('community_id', $discussion->community_id)
            ->where('user_id', $user->user_id)
            ->where('role', 'Ketua Grup')
            ->exists();

        Log::info("User ID yang login: " . $user->user_id);
        Log::info("User ID pemilik diskusi: " . $discussion->user_id);
        Log::info("Apakah dia ketua grup?: " . json_encode($isKetua));
        Log::info("Community ID diskusi: " . $discussion->community_id);


        if (!$isKetua) {
            return ApiResponse::error(
                'Hanya Ketua Grup yang dapat menghapus diskusi.',
                [
                    'reason' => 'Bukan Ketua Grup.',
                    'detail' => 'User tidak memiliki peran sebagai Ketua Grup pada komunitas ini.'
                ],
                403
            );
        }


        DB::beginTransaction();

        try {
            $comments = DiscussionComment::where('discus_id', $discus_id)->get();
            $commentIds = $comments->pluck('comment_id')->toArray();

            if (!empty($commentIds)) {
                DiscussionCommentLike::whereIn('comment_id', $commentIds)->delete();
                DiscussionComment::whereIn('comment_id', $commentIds)->delete();
            }

            DiscussionLike::where('discus_id', $discus_id)->delete();

            $discussion->delete();

            DB::commit();
            return ApiResponse::success('Diskusi dan seluruh datanya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Gagal menghapus diskusi.', ['exception' => $e->getMessage()], 500);
        }
    }
}
