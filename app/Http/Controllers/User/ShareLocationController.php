<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShareLocationController extends Controller
{

    /**
     * @OA\Patch(
     *     path="/profile/share-location",
     *     summary="Toggle Bagikan Lokasi",
     *     description="Mengubah status bagikan lokasi user (true/false). Harus login (JWT)",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"is_share_location"},
     *             @OA\Property(
     *                 property="is_share_location",
     *                 type="boolean",
     *                 example=true,
     *                 description="Status apakah lokasi dibagikan atau tidak"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Status berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status bagikan lokasi berhasil diperbarui."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="string", example="USR123"),
     *                 @OA\Property(property="is_share_location", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal memperbarui status."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function updateShareLocation(Request $request)
    {
        $request->validate([
            'is_share_location' => 'required|boolean',
        ]);

        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $authUser->is_share_location = $request->is_share_location;
            $authUser->save();

            return ApiResponse::success(
                'Status bagikan lokasi berhasil diperbarui.',
                [
                    'user_id'           => $authUser->user_id,
                    'is_share_location' => $authUser->is_share_location
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui status.', ['error' => $e->getMessage()], 500);
        }
    }
}
