<?php

namespace App\Http\Controllers\CheckVersion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppVersion;
use App\Helpers\ApiResponse;
use App\Models\SystemMaster;

/**
 * @OA\Get(
 *     path="/check-version",
 *     summary="Cek versi aplikasi",
 *     tags={"Version"},
 *     @OA\Parameter(
 *         name="version_code",
 *         in="query",
 *         required=true,
 *         description="Kode versi aplikasi yang sedang digunakan user (contoh: 1.0.0)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="platform",
 *         in="query",
 *         required=false,
 *         description="Platform aplikasi (contoh: android atau ios)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil cek versi",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Cek versi berhasil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="app_version",
 *                     type="object",
 *                     @OA\Property(property="platform", type="string", example="android"),
 *                     @OA\Property(property="is_allowed", type="boolean", example=true),
 *                     @OA\Property(property="latest_version", type="string", example="3.0.0"),
 *                     @OA\Property(property="need_update", type="boolean", example=true),
 *                     @OA\Property(property="latest_description", type="string", example="Perbaikan bug dan peningkatan performa"),
 *                     @OA\Property(property="message_detail", type="string", example="Versi terbaru tersedia. Disarankan untuk memperbarui aplikasi Anda.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parameter version_code tidak dikirim",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Parameter version_code wajib diisi."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */
class AppVersionController extends Controller
{
    public function check(Request $request)
    {
        $versionCode = $request->query('version_code');
        $platform = $request->query('platform'); // optional

        if (!$versionCode) {
            return ApiResponse::error('Parameter version_code wajib diisi.', [], 400);
        }

        // Query versi terbaru dan versi yang digunakan user
        $queryLatest = AppVersion::where('is_latest', true);
        $queryCurrent = AppVersion::where('version_code', $versionCode);

        if ($platform) {
            $queryLatest->where('platform', $platform);
            $queryCurrent->where('platform', $platform);
        }

        $latestVersion = $queryLatest->first();
        $currentVersion = $queryCurrent->first();

        $isAllowed = optional($currentVersion)->is_allowed ?? false;
        $needUpdate = $latestVersion && $versionCode !== $latestVersion->version_code;

        // Penjelasan pesan versi
        if (!$currentVersion) {
            $messageDetail = "Versi aplikasi Anda belum terdaftar. Pastikan Anda menggunakan versi terbaru dari sumber resmi.";
        } elseif (!$isAllowed) {
            $messageDetail = "Versi aplikasi Anda tidak didukung lagi. Harap perbarui ke versi terbaru.";
        } elseif ($needUpdate) {
            $messageDetail = "Versi terbaru tersedia. Disarankan untuk memperbarui aplikasi Anda.";
        } else {
            $messageDetail = "Versi aplikasi Anda sudah sesuai dan diperbolehkan.";
        }

        // Ambil data sponsor dari model SystemMaster
        $sponsors = SystemMaster::where('category', 'app_information')
            ->where('sub_category', 'sponsor_by')
            ->where('status', 1)
            ->get(['key', 'value', 'description']);

        return ApiResponse::success('Cek versi berhasil', [
            'app_version' => [
                'platform' => $platform ?? 'unknown',
                'is_allowed' => $isAllowed,
                'latest_version' => $latestVersion->version_code ?? null,
                'need_update' => $needUpdate,
                'message_detail' => $messageDetail,
            ],
            'sponsored_by' => $sponsors,
        ]);
    }
}
