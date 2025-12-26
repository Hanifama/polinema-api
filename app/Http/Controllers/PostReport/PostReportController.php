<?php

namespace App\Http\Controllers\PostReport;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\PostReport;
use App\Models\Post;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostReportController extends Controller
{
    /**
     * @OA\Post(
     *     path="/post/reports",
     *     summary="Laporkan konten (Post Commnunity / Post Wall)",
     *     description="
     * Endpoint ini digunakan untuk melaporkan konten yang dianggap melanggar aturan
     * seperti spam, ujaran kebencian, pornografi, atau konten tidak pantas lainnya.
     *
     * ### Cara Penggunaan:
     * - Tentukan jenis konten yang ingin dilaporkan melalui **report_type**
     * - Isi **salah satu** sesuai jenis kontennya:
     *   - report_type = `discussion`  → untuk forum komunitas gunakan `reported_discus_id` diisi oleh discus_id yang mau di report
     *   - report_type = `post_wall`   → untuk post di wall gunakan `reported_post_id` diisi oleh post_id yang mau di report
     * - Pilih salah satu, dan salah satunya null
     *
     * ",
     *     tags={"Post Report"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"report_type","category_code"},
     *
     *             @OA\Property(
     *                 property="report_type",
     *                 type="string",
     *                 example="discussion",
     *                 description="Jenis konten yang dilaporkan. Pilihan: discussion | post_wall"
     *             ),
     *
     *             @OA\Property(
     *                 property="reported_discus_id",
     *                 type="string",
     *                 nullable=true,
     *                 example="discus-8f2c1d9e-7a34-4c3f-9b1a",
     *                 description="Wajib diisi jika report_type = discussion"
     *             ),
     *
     *             @OA\Property(
     *                 property="reported_post_id",
     *                 type="string",
     *                 nullable=true,
     *                 example="post-7a34-4c3f-9b1a-22dcb9a",
     *                 description="Wajib diisi jika report_type = post_wall"
     *             ),
     *
     *             @OA\Property(
     *                 property="category_code",
     *                 type="string",
     *                 example="SPAM",
     *                 description="Kategori Report (contoh: SPAM, SEXUAL_CONTENT, IMPERSONATION)"
     *             ),
     *
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 nullable=true,
     *                 example="Konten mengandung spam promosi",
     *                 description="Keterangan tambahan dari pelapor (opsional)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Report berhasil dikirim."
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal atau user melaporkan konten sendiri"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Konten yang dilaporkan tidak ditemukan"
     *     ),
     *
     *     @OA\Response(
     *         response=409,
     *         description="User sudah pernah melaporkan konten yang sama"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'report_type'       => 'required|in:discussion,post_wall',
            'reported_discus_id' => 'nullable|string',
            'reported_post_id'  => 'nullable|string',
            'category_code'     => 'required|string|max:100',
            'description'       => 'nullable|string',
        ]);

        $userId = Auth::id();

        /** 
         * DISCUSSION REPORT
         */
        if ($request->report_type === 'discussion') {

            if (!$request->reported_discus_id) {
                return ApiResponse::error('reported_discus_id wajib diisi', 400);
            }

            $discussion = Discussion::where('discus_id', $request->reported_discus_id)->first();
            if (!$discussion) {
                return ApiResponse::error('Discussion tidak ditemukan', 404);
            }

            if ($discussion->user_id === $userId) {
                return ApiResponse::error('Tidak bisa melaporkan diskusi sendiri', 400);
            }

            $exists = PostReport::where([
                'report_type'        => 'discussion',
                'reported_discus_id' => $request->reported_discus_id,
                'reporter_user_id'   => $userId,
            ])->exists();

            if ($exists) {
                return ApiResponse::error('Diskusi ini sudah pernah kamu laporkan', 409);
            }

            PostReport::create([
                'report_type'        => 'discussion',
                'reported_discus_id' => $request->reported_discus_id,
                'reporter_user_id'   => $userId,
                'category_code'      => $request->category_code,
                'description'        => $request->description,
                'status'             => 'pending',
            ]);
        }

        /** 
         * POST WALL REPORT
         */
        if ($request->report_type === 'post_wall') {

            if (!$request->reported_post_id) {
                return ApiResponse::error('reported_post_id wajib diisi', 400);
            }

            $post = Post::where('post_id', $request->reported_post_id)->first();
            if (!$post) {
                return ApiResponse::error('Postingan tidak ditemukan', 404);
            }

            if ($post->user_id === $userId) {
                return ApiResponse::error('Tidak bisa melaporkan postingan sendiri', 400);
            }

            $exists = PostReport::where([
                'report_type'      => 'post_wall',
                'reported_post_id' => $request->reported_post_id,
                'reporter_user_id' => $userId,
            ])->exists();

            if ($exists) {
                return ApiResponse::error('Postingan ini sudah pernah kamu laporkan', 409);
            }

            PostReport::create([
                'report_type'      => 'post_wall',
                'reported_post_id' => $request->reported_post_id,
                'reporter_user_id' => $userId,
                'category_code'    => $request->category_code,
                'description'      => $request->description,
                'status'           => 'pending',
            ]);
        }

        return ApiResponse::success('Report berhasil dikirim');
    }
}
