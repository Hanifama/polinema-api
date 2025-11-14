<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Helpers\ApiResponse;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/articles",
     *     summary="Mendapatkan semua artikel",
     *     description="Mengambil daftar semua artikel yang tersedia.",
     *     tags={"Artikel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Artikel berhasil diambil"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ada artikel ditemukan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized - Tidak terautentikasi"
     *     )
     * )
     */
    public function getAllArticles()
    {
        $articles = Article::all();

        if ($articles->isEmpty()) {
            return ApiResponse::error('Artikel belum dibuat.', [], 404);
        }

        return ApiResponse::success('Artikel berhasil diambil.', $articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/{article_id}",
     *     summary="Mendapatkan detail artikel berdasarkan ID",
     *     description="Mengambil artikel berdasarkan article_id.",
     *     tags={"Artikel"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="article_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artikel berhasil diambil"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artikel tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized - Tidak terautentikasi"
     *     )
     * )
     */
    public function getArticleDetail(string $article_id)
    {
        $article = Article::find($article_id);

        if (!$article) {
            return ApiResponse::error('Artikel tidak ditemukan.', [], 404);
        }

        return ApiResponse::success('Artikel berhasil diambil.', $article);
    }
}
