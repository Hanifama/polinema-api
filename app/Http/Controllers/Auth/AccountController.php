<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class AccountController extends Controller
{

    /**
     * @OA\Post(
     *     path="/change/send-otp",
     *     summary="Kirim OTP untuk perubahan kata sandi",
     *     description="Menghasilkan dan mengirim OTP 6 digit ke email pengguna untuk permintaan perubahan kata sandi. OTP berlaku selama 3 menit.",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Alamat email pengguna yang sudah terautentikasi.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email"},
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Alamat email pengguna.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP berhasil dikirim ke email.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP berhasil dikirim.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Terjadi kesalahan validasi, email tidak valid atau ada masalah lain.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(type="array", items=@OA\Items(type="string"))
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function sendOtpChangePassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 400);
        }

        $otpCode = rand(100000, 999999);

        $user->otp_code = $otpCode;
        $user->otp_expires_at = Carbon::now()->addMinutes(3);
        $user->save();

        $user->notify(new SendOtpNotification($otpCode));

        return ApiResponse::success('OTP berhasil dikirim.');
    }

    /**
     * @OA\Post(
     *     path="/change/verify-otp",
     *     summary="Verifikasi OTP untuk perubahan kata sandi",
     *     description="Memverifikasi OTP yang dikirim ke email pengguna untuk memastikan bahwa OTP valid dan belum kadaluarsa. Jika OTP berhasil diverifikasi, pengguna dapat melanjutkan untuk mengganti kata sandi.",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth":{}}},  
     *     @OA\RequestBody(
     *         required=true,
     *         description="Kode OTP yang dimasukkan oleh pengguna.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"otp_code"},
     *                 @OA\Property(property="otp_code", type="string", format="numeric", example="123456", description="Kode OTP yang dikirim ke email pengguna.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP berhasil diverifikasi, lanjutkan untuk mengganti password.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP berhasil diverifikasi, lanjutkan untuk mengganti password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP tidak valid atau telah kadaluarsa.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP tidak valid atau telah kadaluarsa."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(type="array", items=@OA\Items(type="string"))
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function verifyOtpChangePassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 400);
        }

        if ($request->otp_code !== $user->otp_code || Carbon::now()->gt($user->otp_expires_at)) {
            return ApiResponse::error('OTP tidak valid atau telah kadaluarsa.', [], 400);
        }

        return ApiResponse::success('OTP berhasil diverifikasi, lanjutkan untuk mengganti password.');
    }

    /**
     * @OA\Post(
     *     path="/change/password",
     *     summary="Mengganti kata sandi pengguna",
     *     description="Memungkinkan pengguna untuk mengganti kata sandi mereka setelah memverifikasi OTP dan memastikan kata sandi lama benar.",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth":{}}},  
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data untuk mengganti kata sandi termasuk OTP, kata sandi lama, dan kata sandi baru.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"otp_code", "old_password", "new_password"},
     *                 @OA\Property(property="otp_code", type="string", format="numeric", example="123456", description="Kode OTP yang dimasukkan oleh pengguna untuk verifikasi."),
     *                 @OA\Property(property="old_password", type="string", example="oldpassword123", description="Kata sandi lama pengguna."),
     *                 @OA\Property(property="new_password", type="string", example="newpassword123", description="Kata sandi baru pengguna."),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kata sandi berhasil diubah.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password berhasil diubah.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Terjadi kesalahan dalam memverifikasi OTP, kata sandi lama, atau password baru.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP tidak valid atau telah kadaluarsa."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(type="array", items=@OA\Items(type="string"))
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|numeric|digits:6',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 400);
        }

        if ($request->otp_code !== $user->otp_code || Carbon::now()->gt($user->otp_expires_at)) {
            return ApiResponse::error('OTP tidak valid atau telah kadaluarsa.', [], 400);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return ApiResponse::error('Kata sandi lama salah.', [], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return ApiResponse::success('Password berhasil diubah.');
    }

}
