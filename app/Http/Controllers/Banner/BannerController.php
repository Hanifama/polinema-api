<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Helpers\ApiResponse;

class BannerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/banner/spotlight",
     *     summary="Ambil semua banner spotlight",
     *     tags={"Banner-spotlight"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar banner spotlight",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="List banner aktif berhasil diambil"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="banner_id", type="string", example="banner-123e4567-e89b-12d3-a456-426614174000"),
     *                     @OA\Property(property="image", type="string", example="https://api-polinema.webview.cloud/uploads/banners/spotlights/berlian.png"),
     *                     @OA\Property(property="date_from", type="string", format="date", example="2025-06-01"),
     *                     @OA\Property(property="date_to", type="string", format="date", example="2025-08-01"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="content", type="string", example="Reuni Akbar Alumni Polinema 2025")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $banners = Banner::where('status', 'active')
            ->whereDate('date_from', '<=', now())
            ->whereDate('date_to', '>=', now())
            ->orderBy('date_from', 'desc')
            ->get();

        return ApiResponse::success('List banner berhasil diambil', $banners);
    }

    /**
     * @OA\Get(
     *     path="/banner/spotlight/{banner_id}",
     *     summary="Ambil detail banner spotlight berdasarkan ID",
     *     tags={"Banner-spotlight"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="banner_id",
     *         in="path",
     *         required=true,
     *         description="ID unik dari banner spotlight",
     *         @OA\Schema(type="string", example="banner-123e4567-e89b-12d3-a456-426614174000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil detail banner",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Detail banner berhasil diambil"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="banner_id", type="string", example="banner-123e4567-e89b-12d3-a456-426614174000"),
     *                 @OA\Property(property="image", type="string", example="https://api-polinema.webview.cloud/uploads/banners/spotlights/berlian.png"),
     *                 @OA\Property(property="date_from", type="string", format="date", example="2025-06-01"),
     *                 @OA\Property(property="date_to", type="string", format="date", example="2025-08-01"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="content", type="string", example="Reuni Akbar Alumni Polinema 2025")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Banner tidak ditemukan"
     *     )
     * )
     */
    public function show($banner_id)
    {
        $banner = Banner::where('banner_id', $banner_id)->first();

        if (!$banner) {
            return ApiResponse::error('Banner tidak ditemukan', [], 404);
        }

        return ApiResponse::success('Detail banner berhasil diambil', $banner);
    }
}
