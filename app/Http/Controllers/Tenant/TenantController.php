<?php

namespace App\Http\Controllers\Tenant;

use App\Helpers\ApiResponse;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TenantController extends Controller
{

    /**
     * @OA\Get(
     *     path="/tenants",
     *     summary="Mengambil daftar tenant yang aktif",
     *     description="Mengambil daftar tenant yang aktif dengan filter opsional berdasarkan kategori, nama, kota, dan radius lokasi.",
     *     operationId="getActiveTenants",
     *     tags={"Tenant"},
     *     security={{"bearerAuth": ""}},
     *     @OA\Parameter(
     *         name="tencat_id",
     *         in="query",
     *         description="Filter tenant berdasarkan ID kategori.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Mencari tenant berdasarkan nama.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filter tenant berdasarkan kota (alamat).",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Filter tenant berdasarkan radius dari lokasi user (dalam kilometer). Jika tidak diisi, maka semua tenant ditampilkan.",
     *         required=false,
     *         @OA\Schema(type="number", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response berhasil dengan data tenant",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Daftar tenant berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="tenant_id", type="string", example="9cba3aec-3049-4833-a918-590c84892d1e"),
     *                     @OA\Property(property="tencat_id", type="string", example="6339cf25-0a52-462a-935f-4b3300f7b8f5"),
     *                     @OA\Property(property="name", type="string", example="Tenant 1"),
     *                     @OA\Property(property="address", type="string", example="Jl. Contoh No. 1, Bandung"),
     *                     @OA\Property(property="distance_in_km", type="number", example=2.35),
     *                     @OA\Property(property="distance_display", type="string", example="2.35 km"),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(property="tencat_id", type="string", example="6339cf25-0a52-462a-935f-4b3300f7b8f5"),
     *                         @OA\Property(property="name", type="string", example="Makanan & Minuman"),
     *                         @OA\Property(property="icon", type="string", example="makanan__minuman.png"),
     *                         @OA\Property(property="status", type="string", example="active")
     *                     ),
     *                     @OA\Property(
     *                         property="vouchers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="voucher_id", type="string", example="fc255908-8e49-4522-8f7d-32d4cea37f9d"),
     *                             @OA\Property(property="title", type="string", example="Voucher Diskon 10%"),
     *                             @OA\Property(property="discount_type", type="string", example="percentage"),
     *                             @OA\Property(property="discount_value", type="number", example=10)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Permintaan tidak valid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Parameter input tidak valid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Tidak ada tenant yang ditemukan dengan filter yang diberikan")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $authUser = JWTAuth::parseToken()->authenticate();

        $userLat = $authUser->lat;
        $userLng = $authUser->lng;

        $radius = $request->filled('radius') && is_numeric($request->radius)
            ? floatval($request->radius)
            : null;

        $distanceFormula = "(6371 * acos(
                            cos(radians(?)) *
                            cos(radians(CAST(lat AS DOUBLE PRECISION))) *
                            cos(radians(CAST(lng AS DOUBLE PRECISION)) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(CAST(lat AS DOUBLE PRECISION)))
                        ))";

        $query = Tenant::with([
            'category',
            'vouchers',
            'owner' => function ($q) {
                $q->select('user_id', 'name', 'photo', 'major');
            }
        ])
            ->select('*')
            ->selectRaw("$distanceFormula AS distance", [$userLat, $userLng, $userLat])
            ->where('status', 'active');

        if ($radius !== null) {
            $query->whereRaw("$distanceFormula <= ?", [$userLat, $userLng, $userLat, $radius]);
            $query->orderBy('distance', 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        if ($request->has('tencat_id')) {
            $query->where('tencat_id', $request->tencat_id);
        }

        if ($request->has('search')) {
            $query->whereRaw("name ILIKE ?", ['%' . $request->search . '%']);
        }

        if ($request->has('city')) {
            $keywords = explode(' ', strtolower($request->city));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhereRaw("LOWER(address) LIKE ?", ['%' . $word . '%']);
                }
            });
        }

        $tenants = $query->get()->map(function ($tenant) {
            $distance = floatval($tenant->distance);

            $tenant->distance_in_km = round($distance, 2);
            $tenant->distance_display = $distance < 1
                ? round($distance * 1000) . ' meter'
                : round($distance, 2) . ' km';

            return $tenant;
        });

        if ($tenants->isEmpty()) {
            $message = $radius !== null
                ? 'Tidak ada tenant dalam jarak pencarian ' . $radius . ' km.'
                : 'Tidak ada tenant yang tersedia.';

            return ApiResponse::success($message, []);
        }

        return ApiResponse::success('Daftar tenant berhasil diambil', $tenants);
    }

    /**
     * @OA\Get(
     *     path="/tenant/{tenant_id}",
     *     summary="Mengambil detail tenant berdasarkan ID",
     *     description="Mengambil informasi lengkap tenant berdasarkan ID tenant yang diberikan.",
     *     operationId="getTenantDetail",
     *     tags={"Tenant"},
     *     security={{"bearerAuth": ""}},
     *     @OA\Parameter(
     *         name="tenant_id",
     *         in="path",
     *         required=true,
     *         description="ID dari tenant yang ingin diambil detailnya.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail tenant berhasil diambil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Detail tenant berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="tenant_id", type="string", example="9cba3aec-3049-4833-a918-590c84892d1e"),
     *                 @OA\Property(property="tencat_id", type="string", example="6339cf25-0a52-462a-935f-4b3300f7b8f5"),
     *                 @OA\Property(property="name", type="string", example="Tenant 1"),
     *                 @OA\Property(property="address", type="string", example="Alamat lengkap Tenant 1"),
     *                 @OA\Property(property="category", type="object", 
     *                     @OA\Property(property="tencat_id", type="string", example="6339cf25-0a52-462a-935f-4b3300f7b8f5"),
     *                     @OA\Property(property="name", type="string", example="Makanan & Minuman"),
     *                     @OA\Property(property="icon", type="string", example="makanan__minuman.png"),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 ),
     *                 @OA\Property(
     *                     property="vouchers",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="voucher_id", type="string", example="fc255908-8e49-4522-8f7d-32d4cea37f9d"),
     *                         @OA\Property(property="title", type="string", example="Voucher Diskon Tenant 1"),
     *                         @OA\Property(property="discount_type", type="string", example="percentage"),
     *                         @OA\Property(property="discount_value", type="number", example=10.00)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Tenant tidak ditemukan"),
     *             @OA\Property(property="errors", type="object", 
     *                 @OA\Property(property="tenant_id", type="string", example="Invalid ID")
     *             )
     *         )
     *     )
     * )
     */
    public function show($tenant_id)
    {
        $tenant = Tenant::with([
            'category',
            'vouchers',
            'owner' => function ($q) {
                $q->select('user_id', 'name', 'photo', 'major');
            }
        ])->where('tenant_id', $tenant_id)->first();

        if (!$tenant) {
            return ApiResponse::error('Tenant tidak ditemukan', ['tenant_id' => 'Invalid ID'], 404);
        }

        return ApiResponse::success('Detail tenant berhasil diambil', $tenant);
    }
}
