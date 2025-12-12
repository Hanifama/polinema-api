<?php

namespace App\Http\Controllers\BlockedUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserBlocked;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\ApiResponse;

class UserBlockerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/block-user",
     *     summary="Blokir user",
     *     tags={"User Block"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"blocked_user_id"},
     *             @OA\Property(property="blocked_user_id", type="string", example="user-e27a6eba-e908-4f5f-9a19-a753cebe6e93"),
     *             @OA\Property(property="reason", type="string", example="Mengirim spam")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User berhasil diblokir"),
     *     @OA\Response(response=409, description="User sudah diblokir sebelumnya")
     * )
     */
    public function blockUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'blocked_user_id' => 'required|string|exists:users,user_id',
            'reason' => 'nullable|string'
        ]);

        $exists = UserBlocked::where('blocker_user_id', $user->user_id)
            ->where('blocked_user_id', $request->blocked_user_id)
            ->first();

        if ($exists) {
            return ApiResponse::error('User sudah diblokir sebelumnya.');
        }

        UserBlocked::create([
            'blocker_user_id' => $user->user_id,
            'blocked_user_id' => $request->blocked_user_id,
            'reason' => $request->reason,
            'is_active' => true,
            'created_dt' => now()
        ]);

        return ApiResponse::success('User berhasil diblokir.');
    }


    /**
     * @OA\Post(
     *     path="/unblock-user",
     *     summary="Unblock user",
     *     tags={"User Block"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"blocked_user_id"},
     *             @OA\Property(property="blocked_user_id", type="string", example="user-e27a6eba-e908-4f5f-9a19-a753cebe6e93")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User berhasil di-unblock"),
     *     @OA\Response(response=404, description="Data blokir tidak ditemukan")
     * )
     */
    public function unblockUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'blocked_user_id' => 'required|string|exists:users,user_id'
        ]);

        $data = UserBlocked::where('blocker_user_id', $user->user_id)
            ->where('blocked_user_id', $request->blocked_user_id)
            ->first();

        if (!$data) {
            return ApiResponse::error('Data blokir tidak ditemukan.', 404);
        }

        $data->is_active = false;
        $data->save();

        return ApiResponse::success('User berhasil di-unblock.');
    }


    /**
     * @OA\Get(
     *     path="/check-blocked/{userId}",
     *     summary="Cek apakah user diblokir",
     *     tags={"User Block"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID user yang ingin dicek",
     *         @OA\Schema(type="string", example="user-e27a6eba-e908-4f5f-9a19-a753cebe6e93")
     *     ),
     *     @OA\Response(response=200, description="Status blokir berhasil diambil")
     * )
     */
    public function checkBlocked($userId)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $blocked = UserBlocked::where('blocker_user_id', $user->user_id)
            ->where('blocked_user_id', $userId)
            ->where('is_active', true)
            ->exists();

        return ApiResponse::success('Status blokir berhasil diambil.', [
            'blocked' => $blocked
        ]);
    }


    /**
     * @OA\Get(
     *     path="/blocked-users",
     *     summary="List user yang diblokir",
     *     tags={"User Block"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Berhasil mengambil data user yang diblokir")
     * )
     */
    public function listBlockedUsers()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $list = UserBlocked::with('blockedUser')
            ->where('blocker_user_id', $user->user_id)
            ->where('is_active', true)
            ->get();

        return ApiResponse::success('Berhasil mengambil data user yang diblokir.', $list);
    }
}
