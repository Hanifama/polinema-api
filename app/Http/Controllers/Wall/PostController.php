<?php

namespace App\Http\Controllers\Wall;

use App\Http\Controllers\Controller;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\ApiResponse;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\UserBlocked;

// use App\Models\PostComment;
// use App\Models\PostLike;

class PostController extends Controller
{

    /**
     * @OA\Get(
     *     path="/wall/{user_id}",
     *     summary="Ambil semua postingan dari wall pengguna",
     *     description="Endpoint ini digunakan untuk mengambil semua postingan di wall milik pengguna tertentu, baik yang bersifat pribadi, publik, atau hanya untuk teman. Postingan dapat difilter berdasarkan tipe konten seperti media (gambar/video), tautan, dokumen, atau teks. Endpoint ini memerlukan token Bearer yang valid untuk otentikasi.",
     *     tags={"Wall Post"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID pengguna yang memiliki wall. Gunakan ID pengguna yang sesuai dengan pengguna yang ingin dilihat wall-nya.",
     *         @OA\Schema(type="string", example="user-uuid")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Optional filter untuk tipe konten yang ingin ditampilkan. Tersedia pilihan 'media' untuk gambar dan video, 'link' untuk tautan, 'document' untuk dokumen, dan 'text' untuk postingan berbentuk teks. Jika tidak ditentukan, semua jenis konten akan ditampilkan.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"media", "link", "document", "text"},
     *             example="media"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar postingan dari wall pengguna",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wall posts retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Wall tidak ditemukan, mungkin pengguna tidak memiliki wall atau wall yang diminta tidak ada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Wall tidak ditemukan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - token tidak valid atau tidak disediakan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function showWall(Request $request, $userId)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $loggedInUserId = $user->user_id;

        $isViewingOwnWall = $loggedInUserId === $userId;

        // Ambil optional filter tipe (image, video, document, link, text)
        $filterType = $request->query('type');

        // Ambil daftar user yang diblokir oleh user ini
        $blockedUserIds = UserBlocked::where('blocker_user_id', $loggedInUserId)
            ->where('is_active', 1) // hanya yang aktif
            ->pluck('blocked_user_id')
            ->toArray();

        $posts = Post::with(['author:user_id,name,photo'])
            ->where('wall_owner_id', $userId)
            ->when($filterType, function ($query, $filterType) {
                if ($filterType === 'media') {
                    $query->whereIn('type', ['image', 'video']);
                } else {
                    $query->where('type', $filterType);
                }
            })
            ->whereNotIn('author_id', $blockedUserIds)
            ->where(function ($query) use ($loggedInUserId, $userId, $isViewingOwnWall) {
                $query
                    ->where('author_id', $loggedInUserId)
                    ->orWhere(function ($q) use ($isViewingOwnWall) {
                        if ($isViewingOwnWall) {
                            $q->whereNotNull('post_id');
                        }
                    })
                    ->orWhere(function ($q) use ($userId, $loggedInUserId) {
                        $q->where('author_id', $userId)
                            ->where(function ($privacyQuery) use ($loggedInUserId, $userId) {
                                $privacyQuery
                                    ->where('privacy', 'public')
                                    ->orWhere(function ($inner) use ($loggedInUserId, $userId) {
                                        $inner->where('privacy', 'friends')
                                            ->whereExists(function ($exists) use ($loggedInUserId, $userId) {
                                                $exists->selectRaw(1)
                                                    ->from('friend')
                                                    ->where(function ($f) use ($loggedInUserId, $userId) {
                                                        $f->where('user_id_1', $loggedInUserId)
                                                            ->where('user_id_2', $userId);
                                                    })
                                                    ->orWhere(function ($f) use ($loggedInUserId, $userId) {
                                                        $f->where('user_id_1', $userId)
                                                            ->where('user_id_2', $loggedInUserId);
                                                    })
                                                    ->where('status', 'accepted');
                                            });
                                    })
                                    ->orWhere(function ($inner) use ($loggedInUserId) {
                                        $inner->where('privacy', 'only_me')
                                            ->where('author_id', $loggedInUserId);
                                    });
                            });
                    });
            })
            ->orderByDesc('created_dt')
            ->get()
            ->map(function ($post) use ($userId, $loggedInUserId) {
                $postId = $post->post_id;

                $totalComments = PostComment::where('post_id', $postId)->count();
                $totalLikes    = PostLike::where('post_id', $postId)->count();
                $isLiked       = PostLike::where('post_id', $postId)
                    ->where('user_id', $loggedInUserId)
                    ->exists();

                return [
                    'post_id'       => $postId,
                    'wall_owner_id' => $post->wall_owner_id,
                    'author_id'     => $post->author_id,
                    'content'       => $post->content,
                    'attachment'    => $post->attachment,
                    'type'          => $post->type,
                    'privacy'       => $post->privacy,
                    'created_dt'    => $post->created_dt,
                    'author'        => [
                        'user_id' => $post->author->user_id,
                        'name'    => $post->author->name,
                        'photo'   => $post->author->photo,
                    ],
                    'is_owner'       => $post->author_id === $userId,
                    'is_replied'     => $post->replied,
                    'is_like'        => $isLiked,
                    'total_comments' => $totalComments,
                    'total_likes'    => $totalLikes,

                ];
            });


        return ApiResponse::success('Wall berhasil diambil', $posts);
    }

    /**
     * @OA\Get(
     *     path="/post/{post_id}",
     *     summary="Ambil detail postingan wall berdasarkan post_id",
     *     description="Mengambil detail lengkap dari sebuah postingan wall termasuk informasi author, komentar, like, dan status like oleh user yang login.",
     *     tags={"Wall Post"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         description="ID dari postingan yang ingin ditampilkan (string)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail post berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Detail post berhasil diambil"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="post_id", type="string", example="abc123xyz"),
     *                 @OA\Property(property="author", type="object",
     *                     @OA\Property(property="user_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg")
     *                 ),
     *                 @OA\Property(property="wall_owner_id", type="integer", example=3),
     *                 @OA\Property(property="content", type="string", example="Isi kontennya di sini."),
     *                 @OA\Property(property="attachment", type="string", nullable=true, example="https://example.com/file.pdf"),
     *                 @OA\Property(property="type", type="string", example="text"),
     *                 @OA\Property(property="privacy", type="string", example="public"),
     *                 @OA\Property(property="created_dt", type="string", format="date-time", example="2024-08-01T12:00:00Z"),
     *                 @OA\Property(property="is_like", type="boolean", example=true),
     *                 @OA\Property(property="total_likes", type="integer", example=25),
     *                 @OA\Property(property="total_comments", type="integer", example=5),
     *                 @OA\Property(property="comments", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="comment_id", type="integer", example=101),
     *                         @OA\Property(property="user_comment", type="object",
     *                             @OA\Property(property="user_id", type="integer", example=4),
     *                             @OA\Property(property="name", type="string", example="Jane Smith"),
     *                             @OA\Property(property="photo", type="string", example="https://example.com/photo2.jpg")
     *                         ),
     *                         @OA\Property(property="content", type="string", example="Komentarnya mantap bang!"),
     *                         @OA\Property(property="created_dt", type="string", format="date-time", example="2024-08-01T12:10:00Z")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Oops, postingan tidak ditemukan."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Postingan dengan ID tersebut mungkin sudah dihapus atau tidak tersedia.")
     *             )
     *         )
     *     )
     * )
     */
    public function showPostDetail($post_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $loggedInUserId = $user->user_id;

        $post = Post::with(['author:user_id,name,photo'])
            ->where('post_id', $post_id)
            ->first();

        if (!$post) {
            return ApiResponse::error(
                'Oops, postingan tidak ditemukan.',
                ['Postingan dengan ID tersebut mungkin sudah dihapus atau tidak tersedia.'],
                404
            );
        }

        $isLiked = PostLike::where('post_id', $post_id)
            ->where('user_id', $loggedInUserId)
            ->exists();

        $totalComments = PostComment::where('post_id', $post_id)->count();
        $totalLikes    = PostLike::where('post_id', $post_id)->count();

        $comments = PostComment::with('user:user_id,name,photo')
            ->where('post_id', $post_id)
            ->orderBy('created_dt', 'asc')
            ->get()
            ->map(function ($comment) {
                return [
                    'comment_id' => $comment->comment_id,
                    'user_comment' => [
                        'user_id' => $comment->user->user_id,
                        'name' => $comment->user->name,
                        'photo' => $comment->user->photo,
                    ],
                    'content' => $comment->content,
                    'created_dt' => $comment->created_dt,
                ];
            });

        $data = [
            'post_id' => $post->post_id,
            'author' => [
                'user_id' => $post->author->user_id,
                'name'    => $post->author->name,
                'photo'   => $post->author->photo,
            ],
            'wall_owner_id' => $post->wall_owner_id,
            'content'       => $post->content,
            'attachment'    => $post->attachment,
            'type'          => $post->type,
            'privacy'       => $post->privacy,
            'created_dt'    => $post->created_dt,
            'is_like'       => $isLiked,
            'total_likes'   => $totalLikes,
            'total_comments' => $totalComments,
            'comments'      => $comments,
        ];

        return ApiResponse::success('Detail post berhasil diambil', $data);
    }

    /**
     * @OA\Post(
     *     path="/wall/create-post",
     *     summary="Buat post baru di wall",
     *     description="Endpoint ini digunakan untuk membuat postingan baru di wall pengguna tertentu. Bisa mengandung file atau link sebagai attachment. Harus menggunakan token Bearer yang valid.",
     *     tags={"Wall Post"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"wall_owner_id"},
     *                 @OA\Property(
     *                     property="wall_owner_id",
     *                     type="string",
     *                     example="wall-owner-uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="Ini adalah postingan baru"
     *                 ),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="file",
     *                     description="File yang diunggah (gambar/video/dokumen)"
     *                 ),
     *                 @OA\Property(
     *                     property="link",
     *                     type="string",
     *                     format="url",
     *                     example="https://example.com/linkcontoh"
     *                 ),
     *                 @OA\Property(
     *                     property="privacy",
     *                     type="string",
     *                     enum={"public", "friends", "only_me"},
     *                     example="public"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post berhasil dibuat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=201),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post berhasil dibuat"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Data tidak valid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data tidak valid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - token tidak valid atau tidak disediakan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function createPost(Request $request)
    {
        $request->validate([
            'wall_owner_id' => 'required|string',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,mp4,pdf,doc,docx,xlsx,txt|max:20480',
            'link' => 'nullable|url',
            'privacy' => 'nullable|string|in:public,friends,only_me',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $postId = "wallpost-" . Str::uuid();

        $attachmentUrl = null;
        $type = 'text';

        // Proses upload file jika ada
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $uploadPath = public_path("uploads/wall_attachments/{$user->user_id}");

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $attachmentUrl = url("uploads/wall_attachments/{$user->user_id}/{$filename}");

            $mime = $file->getClientMimeType();
            if (str_contains($mime, 'image')) {
                $type = 'image';
            } elseif (str_contains($mime, 'video')) {
                $type = 'video';
            } elseif (str_contains($mime, 'application') || str_contains($mime, 'text')) {
                $type = 'document';
            }
        }

        // Kalau gak ada file, tapi ada link
        if (!$request->hasFile('attachment') && $request->filled('link')) {
            $type = 'link';
            $attachmentUrl = $request->link;
        }

        // Cek apakah author diblokir oleh pemilik wall
        $isBlocked = UserBlocked::where('blocker_user_id', $request->wall_owner_id)
            ->where('blocked_user_id', $user->user_id)
            ->where('is_active', true)
            ->exists();

        if ($isBlocked) {
            return ApiResponse::error('Anda tidak dapat membuat post karena telah diblokir oleh pemilik wall.');
        }

        // Logic cek apakah boleh bikin post baru
        if ($user->user_id !== $request->wall_owner_id) {
            // Bukan pemilik wall, cek ada post belum dibalas gak
            $pendingPost = Post::where('wall_owner_id', $request->wall_owner_id)
                ->where('author_id', $user->user_id)  // cek hanya post dari author yang sama
                ->where('replied', false)
                ->first();

            if ($pendingPost) {
                return ApiResponse::error('Tidak bisa membuat post baru, ada post yang belum dibalas oleh pemilik wall', 400);
            }
        }

        // PEMILIK WALL BOLEH LANGSUNG POST TANPA UPDATE REPLIED

        // Simpan post baru, replied default false
        $post = Post::create([
            'post_id' => $postId,
            'wall_owner_id' => $request->wall_owner_id,
            'author_id' => $user->user_id,
            'content' => $request->content,
            'attachment' => $attachmentUrl,
            'type' => $type,
            'privacy' => $request->privacy ?? 'public',
            'created_dt' => now(),
            'replied' => false,
        ]);

        return ApiResponse::success('Post berhasil dibuat', $post, 201);
    }

    /**
     * @OA\Post(
     *     path="/post/comment/{post_id}",
     *     summary="Buat komentar baru pada post wall tertentu",
     *     tags={"Wall Post"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         description="ID dari post yang ingin dikomentari",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 description="Isi komentar",
     *                 example="Ini komentar saya"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Komentar berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Komentar berhasil dibuat"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="comment_id", type="string", example="uuid-generated-comment-id"),
     *                 @OA\Property(property="post_id", type="string", example="wallpost-uuid"),
     *                 @OA\Property(property="user_id", type="string", example="user-uuid"),
     *                 @OA\Property(property="content", type="string", example="Ini komentar saya"),
     *                 @OA\Property(property="created_dt", type="string", format="date-time", example="2025-05-21T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The content field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - JWT token tidak valid atau tidak ada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function createComment(Request $request, $post_id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $commentId = Str::uuid();

        $post = Post::findOrFail($post_id);

        // Buat komentar baru
        $comment = PostComment::create([
            'comment_id' => $commentId,
            'post_id' => $post_id,
            'user_id' => $user->user_id,
            'content' => $request->content,
            'created_dt' => now(),
        ]);

        // Kalau user yang komen adalah pemilik wall-nya, dan post itu belum dibalas
        if (
            $user->user_id === $post->wall_owner_id &&
            $post->author_id !== $user->user_id &&
            !$post->replied
        ) {
            $post->replied = true;
            $post->save();
        }

        return ApiResponse::success('Komentar berhasil dibuat', $comment, 201);
    }

    /**
     * @OA\Get(
     *     path="/post/comment/{post_id}",
     *     tags={"Wall Post"},
     *     security={{"bearerAuth":{}}},
     *     summary="Ambil semua komentar berdasarkan ID post wall",
     *     description="Mengambil semua komentar yang terkait dengan post tertentu, termasuk data user yang memberikan komentar.",
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         description="ID dari post yang ingin diambil komentarnya",
     *         required=true,
     *         @OA\Schema(type="string", example="wallpost-afa4f2c0-ef49-4137-a21f-ed32edd82ba3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Komentar berhasil diambil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Komentar berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="comment_id", type="integer", example=12),
     *                     @OA\Property(property="post_id", type="string", example="POST001"),
     *                     @OA\Property(property="user_id", type="string", example="USR123"),
     *                     @OA\Property(property="content", type="string", example="Komentar bagus!"),
     *                     @OA\Property(property="created_dt", type="string", format="date-time", example="2024-08-08 12:34:56"),
     *                     @OA\Property(
     *                         property="user_comment",
     *                         type="object",
     *                         @OA\Property(property="user_id", type="string", example="USR123"),
     *                         @OA\Property(property="name", type="string", example="Budi Santoso"),
     *                         @OA\Property(property="photo", type="string", example="https://example.com/images/budi.jpg")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function getCommentsByPost($post_id)
    {
        // Cek post-nya ada gak
        $post = Post::find($post_id);
        if (!$post) {
            return ApiResponse::error('Post tidak ditemukan', 404);
        }

        // Ambil komentar-komentar yang terkait dengan post_id, beserta data user yg komentar
        $comments = PostComment::with(['user:user_id,name,photo'])
            ->where('post_id', $post_id)
            ->orderBy('created_dt', 'asc')
            ->get()
            ->map(function ($comment) {
                return [
                    'comment_id' => $comment->comment_id,
                    'post_id' => $comment->post_id,
                    'user_id' => $comment->user_id,
                    'content' => $comment->content,
                    'created_dt' => $comment->created_dt,
                    'user_comment' => [
                        'user_id' => $comment->user->user_id,
                        'name' => $comment->user->name,
                        'photo' => $comment->user->photo,
                    ],
                ];
            });

        return ApiResponse::success('Komentar berhasil diambil', $comments);
    }


    /**
     * @OA\Post(
     *     path="/post/like/{post_id}",
     *     tags={"Wall Post"},
     *     summary="Like sebuah post wall",
     *     description="Memberikan like pada sebuah post oleh user yang terautentikasi. User hanya bisa like satu kali.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         description="ID dari post yang ingin di-like",
     *         required=true,
     *         @OA\Schema(type="string", example="POST123")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Like berhasil ditambahkan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Like berhasil ditambahkan"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="like_id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="post_id", type="string", example="POST123"),
     *                 @OA\Property(property="user_id", type="string", example="USR123"),
     *                 @OA\Property(property="created_dt", type="string", format="date-time", example="2024-08-08 12:34:56")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User sudah memberikan like pada post ini",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Anda sudah memberikan like pada post ini")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized (Token tidak valid atau tidak dikirim)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function likePost($post_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $post = Post::find($post_id);
        if (!$post) {
            return ApiResponse::error(
                'Post tidak ditemukan',
                ['Post dengan ID tersebut tidak tersedia di sistem'],
                404
            );
        }

        $likeExist = PostLike::where('post_id', $post_id)
            ->where('user_id', $user->user_id)
            ->exists();

        if ($likeExist) {
            return ApiResponse::error(
                'Anda sudah memberikan like pada post ini',
                ['Pengguna hanya dapat melakukan like satu kali pada sebuah post'],
                400
            );
        }

        $like = PostLike::create([
            'like_id' => Str::uuid(),
            'post_id' => $post_id,
            'user_id' => $user->user_id,
            'created_dt' => now(),
        ]);

        return ApiResponse::success('Like berhasil ditambahkan', $like, 201);
    }

    /**
     * @OA\Get(
     *     path="/posts/{post_id}/likes",
     *     tags={"Wall Post"},
     *     summary="Mendapatkan List Like dari sebuah Wall Post",
     *     description="Menampilkan daftar pengguna yang menyukai postingan wall tertentu",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         description="ID dari post yang ingin dilihat likenya",
     *         @OA\Schema(type="string", example="wallpost-afa4f2c0-ef49-4137-a21f-ed32edd82ba3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data like berhasil diambil.",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data like berhasil diambil."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="post_id", type="string", example="post-12345"),
     *                 @OA\Property(property="likes_count", type="integer", example=5),
     *                 @OA\Property(property="liked_by", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="user_id", type="string", example="user-abc123"),
     *                         @OA\Property(property="name", type="string", example="Andi Wijaya"),
     *                         @OA\Property(property="photo", type="string", nullable=true, example="https://example.com/photo.jpg"),
     *                         @OA\Property(property="liked_at", type="string", format="date-time", example="2025-07-15 13:00:00")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post tidak ditemukan.",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post tidak ditemukan."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function getLikesByPost($post_id)
    {
        $post = Post::where('post_id', $post_id)->first();

        if (!$post) {
            return ApiResponse::error('Post tidak ditemukan.', [], 404);
        }

        // Ambil 20 like terbaru (urut dari terbaru ke terlama)
        $likes = $post->likes()
            ->with('user')
            ->orderBy('created_dt', 'desc')
            ->limit(20)
            ->get();

        $mappedLikes = $likes->map(function ($like) {
            return [
                'user_id' => $like->user->user_id ?? null,
                'name' => $like->user->name ?? null,
                'photo' => $like->user->photo ?? null,
                'liked_at' => $like->created_dt,
            ];
        });

        $data = [
            'post_id' => $post->post_id,
            'likes_count' => $post->likes()->count(), // total semua like
            'liked_by' => $mappedLikes,
        ];

        return ApiResponse::success('Data like berhasil diambil.', $data);
    }



    /**
     * @OA\Delete(
     *     path="/post/{post_id}",
     *     summary="Hapus sebuah post wall",
     *     description="Endpoint ini digunakan untuk menghapus sebuah post yang dibuat oleh pengguna yang sedang login. Harus menggunakan token Bearer yang valid.",
     *     tags={"Wall Post"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         description="ID dari postingan yang ingin dihapus",
     *         @OA\Schema(type="string", example="post-uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post berhasil dihapus",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post tidak ditemukan atau Anda tidak memiliki izin",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post tidak ditemukan atau Anda tidak memiliki izin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - token tidak valid atau tidak disediakan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function deletePost($postId)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $post = Post::where('post_id', $postId)
            ->where('author_id', $user->user_id)
            ->first();

        if (!$post) {
            return ApiResponse::error('Post tidak ditemukan atau Anda tidak memiliki izin', [], 404);
        }

        $post->delete();

        return ApiResponse::success('Post berhasil dihapus');
    }
}
