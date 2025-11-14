<?php

namespace App\Http\Controllers\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\FavoriteFriend;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Friend;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Exception;

class FriendController extends Controller
{
    private function responseJson($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'statusCode' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * @OA\Post(
     *     path="/friend/favorite",
     *     summary="Menambahkan teman ke dalam daftar favorit",
     *     description="Menambahkan teman yang dipilih ke dalam daftar favorit pengguna yang terautentikasi. Jika teman sudah ada dalam daftar favorit, akan memberikan pesan error.",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"friend_user_id"},
     *                 @OA\Property(property="friend_user_id", type="string", example="user-123", description="ID user teman yang akan ditambahkan ke favorit")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menambahkan teman ke favorit",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Teman sudah ada dalam daftar favorit",
     *     ),
     * )
     */
    public function addFavorite(Request $request)
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $friendUserId = $request->input('friend_user_id');

            if ($authUser->user_id === $friendUserId) {
                return ApiResponse::error(
                    'Anda tidak bisa menambahkan diri sendiri ke dalam daftar favorit.',
                    ['friend_user_id' => ['ID tidak boleh sama dengan user.']]
                );
            }

            $existingFavorite = FavoriteFriend::where('user_id', $authUser->user_id)
                ->where('friend_user_id', $friendUserId)
                ->first();

            if ($existingFavorite) {
                return ApiResponse::error('Teman sudah ada dalam daftar favorit Anda.');
            }

            $favorite = FavoriteFriend::create([
                'favorite_id' => 'friendfav-' . Str::uuid(),
                'user_id' => $authUser->user_id,
                'friend_user_id' => $friendUserId,
                'created_dt' => now(),
            ]);

            return ApiResponse::success('Teman berhasil ditambahkan ke favorit.', $favorite);
        } catch (\Exception $e) {
            return ApiResponse::error('Terjadi kesalahan saat menambahkan favorit.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/friend/favorite-remove",
     *     summary="Hapus teman dari daftar favorit",
     *     description="Menghapus teman dari daftar favorit pengguna yang sedang login.",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="friend_user_id", type="string", example="user-123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Teman berhasil dihapus dari favorit",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     * )
     */
    public function removeFavorite(Request $request)
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $friendUserId = $request->input('friend_user_id');

            $existingFavorite = FavoriteFriend::where('user_id', $authUser->user_id)
                ->where('friend_user_id', $friendUserId)
                ->first();

            if (!$existingFavorite) {
                return ApiResponse::error('Teman ini tidak ada dalam daftar favorit Anda.');
            }

            $existingFavorite->delete();

            return ApiResponse::success('Teman berhasil dihapus dari favorit.');
        } catch (\Exception $e) {
            return ApiResponse::error('Terjadi kesalahan saat menghapus favorit.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/friend/send-request",
     *     summary="Mengirimkan permintaan pertemanan",
     *     description="Mengirimkan permintaan pertemanan antara dua pengguna. Pengguna yang mengirimkan permintaan adalah pengguna yang sedang login (user_id_1), sedangkan pengguna yang menerima permintaan adalah pengguna yang dituju (user_id_2).",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"user_id_2"},
     *             @OA\Property(
     *                 property="user_id_2",
     *                 description="ID pengguna yang menerima permintaan pertemanan. Merupakan UUID unik dari pengguna yang dituju.",
     *                 type="string",
     *                 example="e7d0b1a0-4db0-4f32-bb4c-fd212f2b1d7b"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permintaan pertemanan berhasil dikirim.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permintaan pertemanan berhasil dikirim."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Permintaan pertemanan gagal, seperti menambahkan diri sendiri atau permintaan sudah ada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Tidak bisa menambahkan diri sendiri."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal, parameter yang diperlukan tidak valid.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validasi gagal."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(
     *                         property="user_id_2",
     *                         type="array",
     *                         items=@OA\Items(type="string", example="The user_id_2 field is required.")
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Terjadi kesalahan."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error message."
     *             )
     *         )
     *     )
     * )
     */
    public function sendRequest(Request $request)
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $validator = Validator::make($request->all(), [
                'user_id_2' => 'required|exists:users,user_id',
            ]);

            if ($validator->fails()) {
                return $this->responseJson(false, 'Validasi gagal.', $validator->errors(), 422);
            }

            $receiverId = $request->user_id_2;

            if ($authUser->user_id === $receiverId) {
                return $this->responseJson(false, 'Tidak bisa menambahkan diri sendiri.', null, 400);
            }

            $existing = Friend::where(function ($query) use ($authUser, $receiverId) {
                $query->where('user_id_1', $authUser->user_id)
                    ->where('user_id_2', $receiverId);
            })->orWhere(function ($query) use ($authUser, $receiverId) {
                $query->where('user_id_1', $receiverId)
                    ->where('user_id_2', $authUser->user_id);
            })->whereIn('status', ['pending', 'accepted']) // <--- penting nih!
                ->exists();

            if ($existing) {
                return $this->responseJson(false, 'Permintaan pertemanan sudah ada.', null, 400);
            }

            Friend::create([
                'user_id_1' => $authUser->user_id,
                'user_id_2' => $receiverId,
                'created_dt' => now(),
                'status' => 'pending',
            ]);

            return $this->responseJson(true, 'Permintaan pertemanan berhasil dikirim.');
        } catch (Exception $e) {
            return $this->responseJson(false, 'Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/friend/accept-request",
     *     summary="Menerima permintaan pertemanan",
     *     description="Fungsi ini digunakan untuk menerima permintaan pertemanan yang dikirimkan oleh pengguna lain. Pengguna yang menerima permintaan adalah pengguna yang sedang login (user_id_2), dan pengguna yang mengirim permintaan adalah user_id_1. Permintaan pertemanan akan diubah statusnya menjadi 'accepted'.",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"user_id_1"},
     *             @OA\Property(
     *                 property="user_id_1",
     *                 description="ID pengguna yang mengirimkan permintaan pertemanan. Merupakan UUID unik dari pengguna yang mengirimkan permintaan.",
     *                 type="string",
     *                 example="b6f83c15-9442-4d92-98da-3859f3b4d8b9"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permintaan pertemanan berhasil diterima.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permintaan pertemanan diterima."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Permintaan pertemanan gagal, seperti jika tidak ada permintaan yang ditemukan.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permintaan pertemanan tidak ditemukan."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal, parameter yang diperlukan tidak valid.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validasi gagal."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(
     *                         property="user_id_1",
     *                         type="array",
     *                         items=@OA\Items(type="string", example="The user_id_1 field is required.")
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Terjadi kesalahan."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error message."
     *             )
     *         )
     *     )
     * )
     */
    public function acceptRequest(Request $request)
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $validator = Validator::make($request->all(), [
                'user_id_1' => 'required|exists:users,user_id',
            ]);

            if ($validator->fails()) {
                return $this->responseJson(false, 'Validasi gagal.', $validator->errors(), 422);
            }

            $senderId = $request->user_id_1;

            $friend = Friend::where('user_id_1', $senderId)
                ->where('user_id_2', $authUser->user_id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$friend) {
                return $this->responseJson(false, 'Permintaan pertemanan tidak ditemukan.', null, 404);
            }

            Friend::where('user_id_1', $senderId)
                ->where('user_id_2', $authUser->user_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'accepted',
                    'accepted_dt' => now(),
                ]);

            return $this->responseJson(true, 'Permintaan pertemanan diterima.');
        } catch (Exception $e) {
            return $this->responseJson(false, 'Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/friend/get-friends",
     *     summary="Mengambil daftar teman yang diterima",
     *     description="Endpoint ini digunakan untuk mengambil daftar teman yang statusnya sudah diterima ('accepted') oleh pengguna yang sedang login. Pengguna yang sedang login akan dianggap sebagai 'user_id_2' dan teman-temannya dapat ditemukan baik sebagai 'user_id_1' atau 'user_id_2'.",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data teman berhasil diambil.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Data teman berhasil diambil."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="user_id", type="string", example="b6f83c15-9442-4d92-98da-3859f3b4d8b9"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="major", type="string", example="Informatics Engineering"),
     *                     @OA\Property(property="year_generation", type="string", example="2023"),
     *                     @OA\Property(property="job", type="string", example="Software Developer"),
     *                     @OA\Property(property="location", type="string", example="Jakarta, Indonesia"),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
     *                     @OA\Property(property="lat", type="number", format="float", example="-6.1751"),
     *                     @OA\Property(property="lng", type="number", format="float", example="106.8650"),
     *                     @OA\Property(property="user_status", type="string", example="active"),
     *                     @OA\Property(property="friendship_status", type="string", example="accepted"),
     *                     @OA\Property(property="friendship_created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z"),
     *                     @OA\Property(property="friendship_accepted_at", type="string", format="date-time", example="2025-04-11T15:30:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ada teman yang diterima.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Anda belum memiliki teman."
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Terjadi kesalahan."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error message."
     *             )
     *         )
     *     )
     * )
     */
    public function getFriends()
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $friends = Friend::with(['requester', 'receiver'])
                ->where(function ($query) use ($authUser) {
                    $query->where(function ($q) use ($authUser) {
                        $q->where('user_id_1', $authUser->user_id)
                            ->orWhere('user_id_2', $authUser->user_id);
                    })
                        ->where('status', 'accepted');
                })
                ->get()
                ->map(function ($friend) use ($authUser) {
                    $friendUser = $friend->user_id_1 === $authUser->user_id
                        ? $friend->receiver
                        : $friend->requester;

                    return [
                        'user_id' => $friendUser->user_id,
                        'name' => $friendUser->name,
                        'major' => $friendUser->major,
                        'year_generation' => $friendUser->year_generation,
                        'job' => $friendUser->job,
                        'location' => $friendUser->location,
                        'photo' => $friendUser->photo,
                        'lat' => $friendUser->lat,
                        'lng' => $friendUser->lng,
                        'user_status' => $friendUser->status,
                        'friendship_status' => $friend->status,
                        'friendship_created_at' => $friend->created_dt,
                        'friendship_accepted_at' => $friend->accepted_dt,
                    ];
                });

            if ($friends->isEmpty()) {
                return $this->responseJson(true, 'Anda belum memiliki teman.');
            }

            return $this->responseJson(true, 'Data teman berhasil diambil.', $friends);
        } catch (Exception $e) {
            return $this->responseJson(false, 'Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/friend/get-incoming-requests",
     *     summary="Mengambil daftar permintaan pertemanan yang masuk",
     *     description="Endpoint ini digunakan untuk mengambil daftar permintaan pertemanan yang statusnya 'pending' yang diterima oleh pengguna yang sedang login. Permintaan ini berasal dari teman yang mengirimkan permintaan pertemanan dan menunggu konfirmasi dari pengguna yang sedang login.",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Permintaan masuk berhasil diambil.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permintaan masuk berhasil diambil."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="user_id", type="string", example="b6f83c15-9442-4d92-98da-3859f3b4d8b9"),
     *                     @OA\Property(property="name", type="string", example="Jane Smith"),
     *                     @OA\Property(property="major", type="string", example="Computer Science"),
     *                     @OA\Property(property="year_generation", type="string", example="2024"),
     *                     @OA\Property(property="job", type="string", example="Data Analyst"),
     *                     @OA\Property(property="location", type="string", example="Surabaya, Indonesia"),
     *                     @OA\Property(property="lat", type="number", format="float", example="-7.2504"),
     *                     @OA\Property(property="lng", type="number", format="float", example="112.7688"),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
     *                     @OA\Property(property="user_status", type="string", example="active"),
     *                     @OA\Property(property="friendship_status", type="string", example="pending"),
     *                     @OA\Property(property="friendship_created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ada permintaan masuk.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Tidak ada permintaan masuk."
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Terjadi kesalahan."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error message."
     *             )
     *         )
     *     )
     * )
     */
    public function getIncomingRequests()
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $requests = Friend::with('requester')
                ->where('user_id_2', $authUser->user_id)
                ->where('status', 'pending')
                ->get()
                ->map(function ($friend) {
                    $requester = $friend->requester;

                    return [
                        'user_id' => $requester->user_id,
                        'name' => $requester->name,
                        'major' => $requester->major,
                        'year_generation' => $requester->year_generation,
                        'job' => $requester->job,
                        'location' => $requester->location,
                        'lat' => $requester->lat,
                        'lng' => $requester->lng,
                        'photo' => $requester->photo,
                        'user_status' => $requester->status,
                        'friendship_status' => $friend->status,
                        'friendship_created_at' => $friend->created_dt,
                    ];
                });

            if ($requests->isEmpty()) {
                return $this->responseJson(true, 'Tidak ada permintaan masuk.');
            }

            return $this->responseJson(true, 'Permintaan masuk berhasil diambil.', $requests);
        } catch (Exception $e) {
            return $this->responseJson(false, 'Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/friend/get-outgoing-requests",
     *     summary="Mengambil daftar permintaan pertemanan yang keluar",
     *     description="Endpoint ini digunakan untuk mengambil daftar permintaan pertemanan yang statusnya 'pending' yang telah dikirim oleh pengguna yang sedang login kepada pengguna lain. Permintaan ini menunjukkan bahwa pengguna yang sedang login telah mengirim permintaan pertemanan yang menunggu konfirmasi dari pengguna penerima.",
     *     tags={"Friends"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Permintaan keluar berhasil diambil.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permintaan keluar berhasil diambil."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="user_id", type="string", example="b6f83c15-9442-4d92-98da-3859f3b4d8b9"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="major", type="string", example="Electrical Engineering"),
     *                     @OA\Property(property="year_generation", type="string", example="2023"),
     *                     @OA\Property(property="job", type="string", example="Software Engineer"),
     *                     @OA\Property(property="location", type="string", example="Bandung, Indonesia"),
     *                     @OA\Property(property="lat", type="number", format="float", example="-6.9175"),
     *                     @OA\Property(property="lng", type="number", format="float", example="107.6191"),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
     *                     @OA\Property(property="status", type="string", example="inactive"),
     *                     @OA\Property(property="friendship_status", type="string", example="pending"),
     *                     @OA\Property(property="friendship_created_at", type="string", format="date-time", example="2025-04-12T10:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ada permintaan keluar.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Tidak ada permintaan keluar."
     *             ),    
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan pada server.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Terjadi kesalahan."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error message."
     *             )
     *         )
     *     )
     * )
     */
    public function getOutgoingRequests()
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            $requests = Friend::with('receiver')
                ->where('user_id_1', $authUser->user_id)
                ->where('status', 'pending')
                ->get()
                ->map(function ($friend) {
                    $receiver = $friend->receiver;

                    return [
                        'user_id' => $receiver->user_id,
                        'name' => $receiver->name,
                        'major' => $receiver->major,
                        'year_generation' => $receiver->year_generation,
                        'job' => $receiver->job,
                        'location' => $receiver->location,
                        'lat' => $receiver->lat,
                        'lng' => $receiver->lng,
                        'photo' => $receiver->photo,
                        'status' => $receiver->status,
                        'friendship_status' => $friend->status,
                        'friendship_created_at' => $friend->created_dt,
                    ];
                });

            if ($requests->isEmpty()) {
                return $this->responseJson(true, 'Tidak ada permintaan keluar.');
            }

            return $this->responseJson(true, 'Permintaan keluar berhasil diambil.', $requests);
        } catch (Exception $e) {
            return $this->responseJson(false, 'Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
