<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login pengguna",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Email pengguna"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="Password pengguna"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="statusCode",
     *                 type="integer",
     *                 example=200
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Login berhasil."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     description="Token akses JWT"
     *                 ),
     *                 @OA\Property(
     *                     property="token_type",
     *                     type="string",
     *                     example="bearer"
     *                 ),
     *                 @OA\Property(
     *                     property="expires_in",
     *                     type="integer",
     *                     example=3600
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Input tidak valid",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="statusCode",
     *                 type="integer",
     *                 example=400
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Input tidak valid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Daftar kesalahan validasi"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Email belum terdaftar",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="statusCode",
     *                 type="integer",
     *                 example=404
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Email belum terdaftar."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Akun belum aktif",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="statusCode",
     *                 type="integer",
     *                 example=403
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Akun Anda belum aktif."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Email atau password salah",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="statusCode",
     *                 type="integer",
     *                 example=401
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Email atau password salah."
     *             )
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'Input tidak valid.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only('email', 'password');
        $credentials = [
            'email' => strtolower(trim($request->input('email'))),
            'password' => $request->input('password'),
        ];
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'statusCode' => 404,
                'status' => false,
                'message' => 'Email belum terdaftar.',
            ], 404);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'statusCode' => 403,
                'status' => false,
                'message' => 'Akun Anda belum aktif.',
            ], 403);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'statusCode' => 401,
                'status' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
            ]
        ]);
    }
}
