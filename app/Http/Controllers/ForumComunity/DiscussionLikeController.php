<?php

namespace App\Http\Controllers\ForumComunity;

use App\Http\Controllers\Controller;

use App\Models\DiscussionLike;
use App\Models\Discussion;
use App\Helpers\ApiResponse;

use Tymon\JWTAuth\Facades\JWTAuth;

class DiscussionLikeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/like/discussion/{discus_id}",
     *     summary="Tambah Like pada Diskusi",
     *     description="Menambahkan like ke diskusi tertentu oleh user yang sudah login.",
     *     tags={"Menyukai Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID diskusi yang ingin diberi like",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Like berhasil ditambahkan",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Anda sudah menyukai diskusi ini",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Anda sudah menyukai diskusi ini.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diskusi tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function store($discus_id)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;

        $existingLike = DiscussionLike::where('discus_id', $discus_id)
            ->where('user_id', $user_id)
            ->first();

        if ($existingLike) {
            return ApiResponse::error('Anda sudah menyukai diskusi ini.', [], 400);
        }

        $like = new DiscussionLike();
        $like->discus_id = $discus_id;
        $like->user_id = $user_id;
        $like->created_dt = now();
        $like->save();

        $discussion = Discussion::findOrFail($discus_id);
        $discussion->increment('like_cnt');

        return ApiResponse::success('Like berhasil ditambahkan.', $discussion, 201);
    }

    /**
     * @OA\Delete(
     *     path="/like/discussion/{discus_id}",
     *     summary="Hapus Like dari Diskusi",
     *     description="Menghapus like dari diskusi oleh user yang sudah login.",
     *     tags={"Menyukai Diskusi Komunitas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="discus_id",
     *         in="path",
     *         required=true,
     *         description="ID diskusi yang ingin di-unlike",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like berhasil dihapus"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Like tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Like tidak ditemukan.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Diskusi tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Diskusi tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function destroy($discus_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;

        $deleted = DiscussionLike::where('discus_id', $discus_id)
            ->where('user_id', $user_id)
            ->delete();

        if ($deleted === 0) {
            return ApiResponse::error('Like tidak ditemukan.', [], 400);
        }
        $discussion = Discussion::findOrFail($discus_id);
        $discussion->decrement('like_cnt');

        return ApiResponse::success('Like berhasil dihapus.', $discussion, 200);
    }
}
