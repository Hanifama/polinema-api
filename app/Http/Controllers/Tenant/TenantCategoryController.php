<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantCategory;
use App\Helpers\ApiResponse;

class TenantCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tenant-categories",
     *     summary="Mengambil daftar kategori tenant yang aktif",
     *     description="Mengambil semua kategori tenant yang aktif dari database.",
     *     operationId="getTenantCategories",
     *     tags={"Tenant Category"},
     *     security={{"bearerAuth": ""}},
     *     @OA\Response(
     *         response=200,
     *         description="Kategori tenant berhasil diambil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kategori tenant berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="tencat_id", type="string", example="6339cf25-0a52-462a-935f-4b3300f7b8f5"),
     *                     @OA\Property(property="name", type="string", example="Makanan & Minuman"),
     *                     @OA\Property(property="icon", type="string", example="makanan__minuman.png"),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = TenantCategory::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return ApiResponse::success('Kategori tenant berhasil diambil', $categories);
    }
}
