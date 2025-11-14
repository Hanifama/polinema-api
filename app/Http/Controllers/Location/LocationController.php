<?php

namespace App\Http\Controllers\Location;

use App\Models\Location;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/master-data/locations",
     *     summary="Ambil daftar lokasi",
     *     tags={"Master Data"},
     *     description="Mengambil daftar lokasi berdasarkan tipe dan pencarian nama. (baru tersedia untuk provinsi) lat dan lng pada provinsi menunjukan lokasi ibukota pada provinsi tersebut.",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Jenis lokasi, contoh: provinsi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Cari berdasarkan nama lokasi (contoh: Jawa)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar lokasi",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Daftar lokasi berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Jawa Barat"),
     *                     @OA\Property(property="type", type="string", example="provinsi"),
     *                     @OA\Property(property="lat", type="string", example="-6.9039"),
     *                     @OA\Property(property="lng", type="string", example="107.6186")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Location::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('name')) {
            $query->where('name', 'ILIKE', '%' . $request->name . '%');
        }

        $locations = $query->orderBy('order')->get()->makeHidden(['order']);

        return response()->json([
            'statusCode' => 200,
            'status'     => true,
            'message'    => 'Daftar lokasi berhasil diambil',
            'data'       => $locations
        ]);
    }
}
