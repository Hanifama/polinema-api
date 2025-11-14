<?php

namespace App\Http\Controllers\BannerTenant;

use App\Http\Controllers\Controller;
use App\Models\BannerTenant;
use App\Helpers\ApiResponse;

class BannerTenantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/banner/banner-tenant",
     *     tags={"Banner Tenant"},
     *     summary="Ambil semua banner tenant voucher belanja",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List banner tenant berhasil diambil"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan server"
     *     )
     * )
     */
    public function index()
    {
        $banners = BannerTenant::where('status', 'active')
            ->whereDate('date_from', '<=', now())
            ->whereDate('date_to', '>=', now())
            ->orderBy('date_from', 'desc')
            ->get();

        return ApiResponse::success('List banner tenant berhasil diambil', $banners);
    }
}
