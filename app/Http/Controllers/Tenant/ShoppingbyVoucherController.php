<?php

namespace App\Http\Controllers\Tenant;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShoppingbyVoucherController extends Controller
{

    /**
     * @OA\Get(
     *     path="/promo-categories",
     *     summary="Mengambil daftar kategori promo aktif",
     *     description="Menampilkan daftar kategori voucher promo aktif yang dikelompokkan berdasarkan name voucher.",
     *     operationId="listCategoryVouchers",
     *     tags={"Promo"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar voucher aktif berhasil dimuat.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Tidak terautentikasi. Token tidak valid atau tidak ditemukan.",
     *     )
     * )
     */
    public function listVoucherCategories()
    {
        $query = VoucherCategory::select('vocat_id', 'name', 'icon', 'description');

        $categories = $query->get();

        return ApiResponse::success('Kategori voucher berhasil dimuat.', $categories);
    }

    /**
     * @OA\Get(
     *     path="/grouped-promos",
     *     summary="Daftar promosi beserta informasi tenant, dengan filter kategori promo voucher",
     *     tags={"Promo"},
     *     operationId="listVouchers",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="vocat_id",
     *         in="query",
     *         description="Filter voucher berdasarkan ID kategori voucher (vocat_id)",
     *         required=false,
     *         @OA\Schema(type="string", example="vocat-aa7110f7-0f26-45d3-b21b-d39d1772b66b")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar voucher",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Tidak terautentikasi. Token tidak valid atau tidak ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ) 
     * )
     */
    public function listVouchersWithTenant(Request $request)
    {
        $vocatId = $request->get('vocat_id');

        $query = Voucher::with('tenant')
            ->where('status', 'active');

        if ($vocatId) {
            $query->where('vocat_id', $vocatId);
        }

        $vouchers = $query->get()->map(function ($voucher) {
            if ($voucher->discount_type === 'percentage') {
                $voucher->discount_value = rtrim(rtrim($voucher->discount_value, '0'), '.') . '%';
            } elseif ($voucher->discount_type === 'fixed') {
                $voucher->discount_value = 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
            }

            $voucher->minimum_amount = (int) $voucher->minimum_amount == $voucher->minimum_amount
                ? (int) $voucher->minimum_amount
                : $voucher->minimum_amount;

            $voucher->maximum_discount = (int) $voucher->maximum_discount == $voucher->maximum_discount
                ? (int) $voucher->maximum_discount
                : $voucher->maximum_discount;

            $voucher->tenant_name = $voucher->tenant->name ?? null;
            $voucher->tenant_banner = $voucher->tenant->banner ?? null;

            unset($voucher->tenant);

            return $voucher;
        });

        $message = $vocatId
            ? "Voucher dengan kategori ID $vocatId berhasil dimuat."
            : "Semua voucher berhasil dimuat.";

        return ApiResponse::success($message, $vouchers);
    }

    /**
     * @OA\Get(
     *     path="/grouped-promos/{voucher_id}",
     *     summary="Detail promo berdasarkan voucher_id",
     *     tags={"Promo"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="voucher_id",
     *         in="path",
     *         description="ID voucher yang ingin diambil detailnya",
     *         required=true,
     *         @OA\Schema(type="string", example="voucher-1e8dece9-3752-4120-a483-6b8c33b94b5f")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil detail voucher",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Voucher tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Voucher dengan ID tersebut tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function getVoucherDetail($voucher_id)
    {
        $voucher = Voucher::with('tenant')->where('voucher_id', $voucher_id)->first();

        if (!$voucher) {
            return ApiResponse::error("Voucher dengan ID tersebut tidak ditemukan.", 404);
        }

        if ($voucher->discount_type === 'percentage') {
            $voucher->discount_value = rtrim(rtrim($voucher->discount_value, '0'), '.') . '%';
        } elseif ($voucher->discount_type === 'fixed') {
            $voucher->discount_value = 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
        }

        $voucher->minimum_amount = (int) $voucher->minimum_amount == $voucher->minimum_amount
            ? (int) $voucher->minimum_amount
            : $voucher->minimum_amount;

        $voucher->maximum_discount = (int) $voucher->maximum_discount == $voucher->maximum_discount
            ? (int) $voucher->maximum_discount
            : $voucher->maximum_discount;

        $voucher->tenant_name = $voucher->tenant->name ?? null;
        $voucher->tenant_banner = $voucher->tenant->banner ?? null;

        unset($voucher->tenant);

        return ApiResponse::success("Detail voucher berhasil dimuat.", $voucher);
    }
}
