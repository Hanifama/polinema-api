<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Notifications\SendOtpNotification;
use Exception;

class RegisterController extends Controller
{

    /**
     * @OA\Post(
     *     path="/auth/register/step1",
     *     summary="Registrasi Langkah 1",
     *     tags={"Auth"},
     *     description="Mendaftarkan akun baru menggunakan email, nama, password, dan provinsi.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="Testing Akun Ajalah"),
     *             @OA\Property(property="email", type="string", format="email", example="testingakunajalah@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123"),
     *             @OA\Property(property="province", type="string", example="Jawa Barat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registrasi berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email berhasil didaftarkan! Silakan lanjutkan pengisian data selanjutnya."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="string", example="usr-12345678-uuid"),
     *                 @OA\Property(property="email", type="string", example="testingakunajalah@gmail.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal atau permintaan tidak valid",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal mendaftarkan pengguna. Email sudah terdaftar.")
     *         )
     *     )
     * )
     */
    public function registerStep1(Request $request)
    {
        try {

            $request->merge([
                'email' => strtolower($request->input('email'))
            ]);

            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'province' => 'required|string|max:150',
            ]);

            $domain = substr(strrchr($validated['email'], "@"), 1);
            if (!checkdnsrr($domain, 'MX')) {
                return response()->json([
                    'statusCode' => 422,
                    'status'     => false,
                    'message'    => 'Email tidak valid.',
                ], 400);
            }

            $user = User::create([
                'user_id'    => 'usr-' . Str::uuid(),
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'province' => $validated['province'],
                'status'     => 'inactive',
                'role'       => 'user',
                'created_dt' => Carbon::now()->format('Y-m-d H:i'),
            ]);

            return response()->json([
                'statusCode' => 200,
                'status'     => true,
                'message'    => 'Email berhasil didaftarkan! Silakan lanjutkan pengisian data selanjutnya.',
                'data'       => [
                    'user_id' => $user->user_id,
                    'email'   => $user->email
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => $e->getCode() ?: 400,
                'status'     => false,
                'message'    => 'Gagal mendaftarkan pengguna. ' . $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/register/step2",
     *     summary="Registrasi Langkah 2",
     *     tags={"Auth"},
     *     description="Melengkapi data jurusan dan angkatan pengguna. setelah berhasil maka akan mengirimkan OTP ke email untuk proses aktifasi akun pengguna.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","email","major","year_generation"},
     *             @OA\Property(property="user_id", type="string", example="usr-12345678-uuid"),
     *             @OA\Property(property="email", type="string", format="email", example="testingakunajalah@gmail.com"),
     *             @OA\Property(property="major", type="string", example="Teknik Informatika"),
     *             @OA\Property(property="year_generation", type="integer", example=2021)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data berhasil dilengkapi dan OTP dikirim",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data jurusan dan angkatan berhasil disimpan. Silakan cek email untuk mendapatkan kode OTP."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="otp_expiry", type="string", format="date-time", example="2025-04-12 15:30")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal atau data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal melengkapi data. Email tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function registerStep2(Request $request)
    {
        try {

            $request->merge([
                'email' => strtolower($request->input('email'))
            ]);

            $validated = $request->validate([
                'user_id' => 'required|string|exists:users,user_id',
                'email' => 'required|email|exists:users,email',
                'major' => 'required|string',
                'year_generation' => 'required|numeric'
            ]);

            $user = User::where('user_id', $validated['user_id'])
                ->where('email', $validated['email'])
                ->first();

            $domain = substr(strrchr($validated['email'], "@"), 1);
            if (!checkdnsrr($domain, 'MX')) {
                return response()->json([
                    'statusCode' => 400,
                    'status'     => false,
                    'message'    => 'Email tidak valid.',
                ], 400);
            }

            $user->update([
                'major' => $validated['major'],
                'year_generation' => $validated['year_generation']
            ]);

            $otp = rand(100000, 999999);
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(2),
            ]);
            $user->notify(new SendOtpNotification($otp));

            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'message' => 'Data jurusan dan angkatan berhasil disimpan. Silakan cek email untuk mendapatkan kode OTP.',
                'data' => [
                    'otp_expiry' => Carbon::now()->addMinutes(2)->format('Y-m-d H:i'),
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'statusCode' => 422,
                'status' => false,
                'message' => 'Validasi gagal.' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'message' => 'Gagal melengkapi data. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/activate",
     *     summary="Aktivasi Akun",
     *     tags={"Auth"},
     *     description="Mengaktifkan akun pengguna menggunakan OTP yang dikirim melalui email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","email","otp_code"},
     *             @OA\Property(property="user_id", type="string", example="usr-12345678-uuid"),
     *             @OA\Property(property="email", type="string", format="email", example="testingakunajalah@gmail.com"),
     *             @OA\Property(property="otp_code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Akun berhasil diaktivasi",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Akun berhasil dibuat!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP salah atau kadaluarsa",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kode OTP yang Anda masukkan salah."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="otp_retry_time", type="string", format="date-time", example="2025-04-12T10:30:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function activate(Request $request)
    {
        try {

            $request->merge([
                'email' => strtolower($request->input('email'))
            ]);

            $validated = $request->validate([
                'user_id' => 'required|string|exists:users,user_id',
                'email' => 'required|email|exists:users,email',
                'otp_code' => 'required|string|size:6'
            ]);

            $user = User::where('user_id', $validated['user_id'])
                ->where('email', $validated['email'])
                ->first();

            if ($user->otp_code === $validated['otp_code']) {
                if ($user->otp_expires_at->isFuture()) {
                    $user->update([
                        'status' => 'active',
                        'otp_code' => null,
                        'otp_expires_at' => null
                    ]);

                    return response()->json([
                        'statusCode' => 200,
                        'status' => true,
                        'message' => 'Akun berhasil dibuat!',
                        'data' => [
                            'status' => 'active'
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'statusCode' => 400,
                        'status' => false,
                        'message' => 'Kode OTP Anda telah kadaluarsa. Silakan coba kirim ulang untuk mendapatkan kode OTP yang baru.',
                        'data' => [
                            'otp_retry_time' => Carbon::now()->addMinutes(1)->toISOString()
                        ]
                    ], 400);
                }
            }

            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'Kode OTP yang Anda masukkan salah.',
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => $e->getCode() ?: 400,
                'status' => false,
                'message' => 'Gagal mengaktivasi akun. ' . $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/resend-otp",
     *     summary="Kirim Ulang OTP",
     *     tags={"Auth"},
     *     description="Mengirim ulang kode OTP ke email pengguna jika OTP sebelumnya sudah kadaluarsa.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","email"},
     *             @OA\Property(property="user_id", type="string", example="usr-12345678-uuid"),
     *             @OA\Property(property="email", type="string", format="email", example="testingakunajalah@gmail.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP berhasil dikirim ulang",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP baru telah dikirim ke email Anda."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="otp_expiry", type="string", format="date-time", example="2025-04-12T10:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP masih valid",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP Anda masih valid, tidak perlu mengirim ulang OTP.")
     *         )
     *     )
     * )
     */
    public function resendOtp(Request $request)
    {
        try {

            $request->merge([
                'email' => strtolower($request->input('email'))
            ]);

            $validated = $request->validate([
                'user_id' => 'required|string|exists:users,user_id',
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('user_id', $validated['user_id'])
                ->where('email', $validated['email'])
                ->first();

            if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
                $otp = rand(100000, 999999);
                $user->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => Carbon::now()->addMinutes(2),
                ]);
                $user->notify(new SendOtpNotification($otp));

                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'message' => 'OTP baru telah dikirim ke email Anda.',
                    'data' => [
                        'otp_expiry' => Carbon::now()->addMinutes(2)->toISOString(),
                    ]
                ], 200);
            }

            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'OTP Anda masih valid, tidak perlu mengirim ulang OTP.',
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'statusCode' => $e->getCode() ?: 500,
                'status' => false,
                'message' => 'Gagal mengirim OTP ulang. ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
