<?php

namespace App\Http\Controllers\ForumComunity;

use App\Http\Controllers\Controller;
use App\Models\DiscussionComment;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\ApiResponse;
use App\Models\DiscussionCommentLike;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DiscussionCommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/discussion/comments/{discus_id}",
     *     summary="Lihat Komentar Diskusi",
     *     description="Menampilkan semua komentar dari sebuah diskusi berdasarkan ID diskusi.",
     *     tags={"Komentar Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID dari diskusi yang ingin diambil komentarnya",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Komentar berhasil diambil"
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
     *     )
     * )
     */
    public function index($discus_id)
    {
        // Ambil diskusi
        $discussion = Discussion::find($discus_id);

        if (!$discussion) {
            return ApiResponse::error('Diskusi tidak ditemukan.', [], 404);
        }

        // Ambil user dari token
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;

        // Ambil komentar tanpa likes 
        $comments = DiscussionComment::with(['user:user_id,name,photo'])
            ->where('discus_id', $discus_id)
            ->orderBy('created_dt', 'desc')
            ->get();

        // ambil likes satu per satu jika tidak kosong
        if ($comments->isNotEmpty()) {
            $comments = $comments->map(function ($comment) use ($user_id) {
                $likes = $comment->likes; 
                $comment->total_like = $likes->count();
                $comment->is_like = $likes->contains('user_id', $user_id);
                unset($comment->likes);
                return $comment;
            });
        }

        return ApiResponse::success('Komentar berhasil diambil.', $comments);
    }

    /**
     * @OA\Post(
     *     path="/discussion/comments/{discus_id}",
     *     summary="Tambah Komentar Diskusi",
     *     description="Menambahkan komentar ke dalam diskusi berdasarkan ID diskusi. Bisa menyertakan lampiran (attachment).",
     *     tags={"Komentar Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID diskusi yang ingin dikomentari",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"content"},
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     description="Isi komentar",
     *                     example="Komentar saya terhadap topik ini..."
     *                 ),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="string",
     *                     format="binary",
     *                     description="Lampiran opsional (max 2MB)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Komentar berhasil ditambahkan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diskusi tidak ditemukan.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validasi gagal."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"content": {"The content field is required."}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function store(Request $request, $discus_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 422);
        }

        if (!Discussion::find($discus_id)) {
            return ApiResponse::error('Diskusi tidak ditemukan.', [], 404);
        }

        $filePath = null;
        $mimeType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $uploadPath = public_path("uploads/discussion_attachments/{$user->user_id}");

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            $filePath = url("uploads/discussion_attachments/{$user->user_id}/{$fileName}");
            $mimeType = $file->getClientMimeType();
        }

        $comment = DiscussionComment::create([
            'comment_id' => 'comment-' . Str::uuid(),
            'user_id' => $user->user_id,
            'discus_id' => $discus_id,
            'content' => $request->content,
            'attachment' => $filePath,
            'attachment_mime' => $mimeType,
            'created_dt' => now(),
        ]);

        $comment->load(['user:user_id,name,photo']);

        return ApiResponse::success('Komentar berhasil ditambahkan.', $comment);
    }

    /**
     * @OA\Post(
     *     path="/comment/like/{comment_id}",
     *     tags={"Komentar Diskusi Komunitas"},
     *     summary="Menyukai komentar diskusi",
     *     description="Memberi like pada komentar diskusi tertentu oleh user yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         description="ID komentar yang ingin disukai",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Komentar berhasil disukai",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Komentar sudah disukai sebelumnya",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Komentar tidak ditemukan",
     *     )
     * )
     */
    public function likeComment($comment_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $comment = DiscussionComment::find($comment_id);
        if (!$comment) {
            return ApiResponse::error('Komentar tidak ditemukan.', [], 404);
        }

        $alreadyLiked = DiscussionCommentLike::where('comment_id', $comment_id)
            ->where('user_id', $user->user_id)
            ->exists();

        if ($alreadyLiked) {
            return ApiResponse::error('Anda sudah menyukai komentar ini.', [], 400);
        }

        DiscussionCommentLike::create([
            'user_id' => $user->user_id,
            'comment_id' => $comment_id,
            'created_dt' => Carbon::now(),
        ]);

        return ApiResponse::success('Komentar berhasil disukai.');
    }

    /**
     * @OA\post(
     *     path="/comment/unlike/{comment_id}",
     *     tags={"Komentar Diskusi Komunitas"},
     *     summary="Menghapus like dari komentar diskusi",
     *     description="Menghapus like yang sebelumnya diberikan oleh user yang sedang login ke komentar tertentu.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         description="ID komentar yang ingin di-unlike",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Komentar berhasil di-unlike",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Komentar belum pernah disukai sebelumnya",
     *     )
     * )
     */
    public function unlikeComment($comment_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $existingLike = DiscussionCommentLike::where('comment_id', $comment_id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$existingLike) {
            return ApiResponse::error(
                'Anda belum menyukai komentar ini.',
                ['error' => 'LIKE_NOT_FOUND'],
                400
            );
        }

        $existingLike->delete();

        return ApiResponse::success('Berhasil membatalkan disukai.');
    }
}
