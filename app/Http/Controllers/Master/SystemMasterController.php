<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemMaster;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;

class SystemMasterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/system-master",
     *     tags={"System Master"},
     *     summary="Ambil semua data System Master",
     *     description="
     *
     * Endpoint ini digunakan untuk mengambil data dari tabel `system_master`.
     * 
     * Bisa difilter berdasarkan:
     * - `category` → contoh: `app_information`
     * - `sub_category` → contoh: `about`, `terms`, `privacy`, `faqs`
     * - `key` → contoh: `about_app`, `terms_conditions`, `privacy_policy`, `faqs`
     * 
     * Contoh penggunaan filter untuk tampilan di frontend:
     * - Tentang Aplikasi: `category=app_information&sub_category=about&key=about_app`
     * - Syarat & Ketentuan: `category=app_information&sub_category=terms&key=terms_conditions`
     * - Kebijakan Privasi: `category=app_information&sub_category=privacy&key=privacy_policy`
     * - FAQs: `category=app_information&sub_category=faqs&key=faqs`
     * 
     * Status akan ditampilkan sebagai `active` jika bernilai 1, dan `inactive` jika bernilai 0.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         description="Filter berdasarkan kategori, contoh: app_information",
     *         @OA\Schema(type="string", example="app_information")
     *     ),
     *     @OA\Parameter(
     *         name="sub_category",
     *         in="query",
     *         required=false,
     *         description="Filter berdasarkan subkategori, contoh: about, terms, privacy, faqs",
     *         @OA\Schema(type="string", example="about")
     *     ),
     *     @OA\Parameter(
     *         name="key",
     *         in="query",
     *         required=false,
     *         description="Filter berdasarkan key, contoh: about_app, terms_conditions, privacy_policy, faqs",
     *         @OA\Schema(type="string", example="about_app")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="category", type="string", example="app_information"),
     *                     @OA\Property(property="sub_category", type="string", example="about"),
     *                     @OA\Property(property="key", type="string", example="about_app"),
     *                     @OA\Property(property="value", type="string", example="<p>HI!Polinema adalah aplikasi resmi alumni...</p>"),
     *                     @OA\Property(property="description", type="string", example="Tentang Aplikasi HI!Polinema"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="created_dt", type="string", example="2025-07-15 13:00:00"),
     *                     @OA\Property(property="created_by", type="string", example="system")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = SystemMaster::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('sub_category')) {
            $query->where('sub_category', $request->sub_category);
        }

        if ($request->has('key')) {
            $query->where('key', $request->key);
        }

        $rawData = $query->where('status', 1)->orderBy('created_dt', 'desc')->get();

        // Mapping agar status jadi "active"/"inactive"
        $data = $rawData->map(function ($item) {
            return [
                'id' => $item->id,
                'category' => $item->category,
                'sub_category' => $item->sub_category,
                'key' => $item->key,
                'value' => $item->value,
                'description' => $item->description,
                'status' => (bool) $item->status, // boolean
                'status_label' => $item->status == 1 ? 'active' : 'inactive', // string
                'created_dt' => $item->created_dt,
                'created_by' => $item->created_by,
            ];
        });

        return ApiResponse::success('Data system master berhasil diambil.', $data);
    }


    /**
     * @OA\Get(
     *     path="/system-master/{id}",
     *     tags={"System Master"},
     *     summary="Ambil satu data System Master berdasarkan ID",
     *     description="Mengambil satu entri dari tabel system_master",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Data berhasil diambil.",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category", type="string", example="faq"),
     *                 @OA\Property(property="key", type="string", example="faq_1"),
     *                 @OA\Property(property="value", type="string", example="Isi konten..."),
     *                 @OA\Property(property="description", type="string", example="Untuk halaman FAQ")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan."
     *     )
     * )
     */
    public function show($id)
    {
        $item = SystemMaster::find($id);

        if (!$item) {
            return ApiResponse::error('Data tidak ditemukan.', [], 404);
        }

        return ApiResponse::success('Detail data berhasil diambil.', $item);
    }

    /**
     * @OA\Put(
     *     path="/system-master/{id}",
     *     tags={"System Master"},
     *     summary="Update value dari System Master",
     *     description="Update value (isi konten) berdasarkan ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"value"},
     *             @OA\Property(property="value", type="string", example="Konten terbaru dari admin...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diperbarui."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="value", type="string", example="Konten terbaru dari admin...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = SystemMaster::find($id);

        if (!$item) {
            return ApiResponse::error('Data tidak ditemukan.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors()->all(), 422);
        }

        $item->value = $request->value;
        $item->save();

        return ApiResponse::success('Data berhasil diperbarui.', $item);
    }
}
