<?php

namespace App\Http\Controllers\Chattings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Helpers\ApiResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{

    /**
     * @OA\Post(
     *     path="/chat/room",
     *     summary="Buat atau ambil chat room antara pengguna yang login dan penerima",
     *     description="Endpoint ini digunakan untuk membuat chat room baru atau mengambil room yang sudah ada antara dua pengguna. Diperlukan token otentikasi Bearer JWT.",
     *     tags={"Chating"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_id"},
     *             @OA\Property(property="recipient_id", type="string", example="user-12345678-aaaa-bbbb-cccc-1234567890ab", description="User ID dari penerima yang ingin diajak chat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil membuat atau mengambil chat room",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Chat room berhasil dibuat."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="chroom_id", type="string", example="room-9ebdee55-fd52-4664-8be8-21b33064c79a"),
     *                 @OA\Property(property="user_1", type="string", example="user-aaa"),
     *                 @OA\Property(property="user_2", type="string", example="user-bbb"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal atau parameter tidak sesuai",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal membuat chat room."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function createRoom(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $request->validate([
                'recipient_id' => 'required|exists:users,user_id'
            ]);

            $wasCreated = false;
            $room = ChatRoom::getOrCreatePrivateRoom($user->user_id, $request->recipient_id, $wasCreated);

            $message = $wasCreated ? 'Chat room berhasil dibuat.' : 'Chat room dimuat.';
            return ApiResponse::success($message, $room);
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal membuat chat room.', [$e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/chat/get-message/{chroom_id}",
     *     summary="Ambil semua pesan dari sebuah chat room",
     *     description="Endpoint ini digunakan untuk mengambil semua pesan dalam sebuah chat room tertentu. Hanya bisa diakses oleh anggota room tersebut. Token Bearer diperlukan.",
     *     tags={"Chating"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="chroom_id",
     *         in="path",
     *         required=true,
     *         description="ID dari chat room yang ingin diambil pesannya",
     *         @OA\Schema(type="string", example="room-uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar pesan"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Pengguna bukan anggota dari chat room ini"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pesan tidak ditemukan dalam chat room ini"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - token tidak valid atau tidak disediakan"
     *     )
     * )
     */
    public function getMessages($chroom_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!ChatRoom::find($chroom_id)?->isUserMember($user->user_id)) {
                return ApiResponse::error('Pengguna bukan anggota chat room ini.', [], 403);
            }

            $messages = ChatMessage::with(['user:user_id,name,photo'])
                ->where('chroom_id', $chroom_id)
                ->orderBy('created_dt', 'asc')
                ->get();

            if ($messages->isEmpty()) {
                return ApiResponse::error('Pesan tidak ditemukan.', [], 404);
            }

            return ApiResponse::success('Pesan berhasil diambil.', $messages->map(function ($msg) {
                return [
                    'user_id' => $msg->user_id,
                    'chroom_id' => $msg->chroom_id,
                    'message' => $msg->message,
                    'attachment' => $msg->attachment,
                    'attachment_mime' => $msg->attachment_mime,
                    'created_dt' => $msg->created_dt,
                    'is_deliver' => $msg->is_deliver,
                    'is_read' => $msg->is_read,
                    'name' => $msg->user?->name,
                    'photo' => $msg->user?->photo,
                ];
            }));
        } catch (\Exception $e) {
            return ApiResponse::error('Terjadi kesalahan.', [$e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/chat/send-message/{chroom_id}",
     *     summary="Kirim pesan ke dalam chat room",
     *     description="Endpoint ini digunakan untuk mengirimkan pesan teks dan/atau file ke dalam sebuah chat room tertentu. Diperlukan token Bearer.",
     *     tags={"Chating"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="chroom_id",
     *         in="path",
     *         required=true,
     *         description="ID dari chat room tempat pesan dikirim",
     *         @OA\Schema(type="string", example="room-uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Isi pesan yang dikirim",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"message"},
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="Isi pesan",
     *                     example="Halo semua!"
     *                 ),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="file",
     *                     description="File yang ingin dilampirkan (opsional). Hanya mendukung jpeg, jpg, png, gif, pdf, docx, txt. Maksimal 10MB"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pesan berhasil dikirim"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Pengguna bukan anggota dari chat room ini"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal (misal: pesan kosong atau file tidak sesuai ketentuan)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - token tidak valid atau tidak disediakan"
     *     )
     * )
     */
    public function sendMessage(Request $request, $chroom_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $request->validate([
                'message' => 'required|string|max:500',
                'attachment' => 'nullable|file|mimes:jpeg,jpg,png,gif,pdf,docx,txt|max:10240',
            ]);

            if (!ChatRoom::find($chroom_id)?->isUserMember($user->user_id)) {
                return ApiResponse::error('Pengguna bukan anggota chat room ini.', [], 403);
            }

            $attachmentUrl = null;
            $attachmentMime = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $uploadPath = public_path("uploads/chat_attachments/{$chroom_id}");

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);

                $attachmentUrl = url("uploads/chat_attachments/{$chroom_id}/{$filename}");
                $attachmentMime = $file->getClientMimeType();
            }

            $message = ChatMessage::create([
                'user_id' => $user->user_id,
                'chroom_id' => $chroom_id,
                'message' => $request->message,
                'attachment' => $attachmentUrl,
                'attachment_mime' => $attachmentMime,
                'created_dt' => now(),
                'is_deliver' => false,
                'is_read' => false,
            ]);

            return ApiResponse::success('Pesan berhasil dikirim.', $message);
        } catch (\Exception $e) {
            return ApiResponse::error('Terjadi kesalahan.', [$e->getMessage()]);
        }
    }
}
