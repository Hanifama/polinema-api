<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PasswordResetController extends Controller
{

    /**
     * @OA\Post(
     *     path="/account/reset/send-otp",
     *     operationId="sendOtpPasswordReset",
     *     tags={"Lupa Password"},
     *     summary="Mengirimkan OTP untuk reset password",
     *     description="Endpoint ini mengirimkan kode OTP ke email pengguna yang diminta untuk mereset password. Kode OTP ini berlaku selama 2 menit dan tidak memerlukan token bearer seperti pada umumnya.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="Alamat email yang digunakan untuk mengirimkan OTP"
     *                 ),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP berhasil dikirim ke email",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP berhasil dikirim ke email."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal atau email tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal."),
     *             @OA\Property(property="errors", type="object", additionalProperties=true)
     *         )
     *     ),
     * )
     */
    public function sendOtpPasswordReset(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
            ],
            [
                'email.exists' => 'Email tidak ditemukan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
            ]
        );


        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();
        $otpCode = rand(100000, 999999);

        $user->otp_code = $otpCode;
        $user->otp_expires_at = Carbon::now()->addMinutes(2);
        $user->save();

        $user->notify(new SendOtpNotification($otpCode));

        return ApiResponse::success('OTP berhasil dikirim ke email.');
    }

    /**
     * @OA\Post(
     *     path="/account/reset/verify",
     *     operationId="verifyOtpPasswordReset",
     *     tags={"Lupa Password"},
     *     summary="Verifikasi OTP dan reset password",
     *     description="Endpoint ini memverifikasi OTP yang dikirim ke email pengguna. Jika OTP valid dan belum kadaluarsa, password baru yang acak akan dibuat dan langsung dikirimkan ke email pengguna. Password baru tersebut bisa langsung digunakan untuk login.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "otp_code"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="Alamat email yang terdaftar dan digunakan untuk verifikasi OTP"
     *                 ),
     *                 @OA\Property(
     *                     property="otp_code",
     *                     type="string",
     *                     description="Kode OTP yang diterima melalui email (6 digit)"
     *                 ),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password baru berhasil dikirim ke email",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP tidak valid, kadaluarsa, atau validasi gagal",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP tidak valid atau telah kadaluarsa."),
     *             @OA\Property(property="errors", type="object", additionalProperties=true)
     *         )
     *     ),
     * )
     */
    public function verifyOtpPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($request->otp_code !== $user->otp_code || Carbon::now()->gt($user->otp_expires_at)) {
            return ApiResponse::error('OTP tidak valid atau telah kadaluarsa.', [], 400);
        }

        return ApiResponse::success('OTP valid. Silakan lanjutkan untuk atur password baru.');
    }

    /**
     * @OA\Post(
     *     path="/account/reset-password",
     *     tags={"Lupa Password"},
     *     summary="Atur password baru setelah OTP",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp_code", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="otp_code", type="string", example="123456"),
     *             @OA\Property(property="new_password", type="string", example="newsecurepass"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="newsecurepass")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password berhasil disimpan"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP salah atau data tidak valid"
     *     )
     * )
     */
    public function setNewPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|numeric|digits:6',
            'new_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', $validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($request->otp_code !== $user->otp_code || Carbon::now()->gt($user->otp_expires_at)) {
            return ApiResponse::error('OTP tidak valid atau telah kadaluarsa.', [], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return ApiResponse::success('Password baru berhasil disimpan. Silakan login.');
    }
}
