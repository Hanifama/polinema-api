<?php

namespace App\Http\Controllers\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;



class ProfileController extends Controller
{

    /**
     * @OA\Get(
     *     path="/profile",
     *     summary="Mengambil profil pengguna",
     *     description="Mengambil data profil pengguna yang terautentikasi token bearer.",
     *     operationId="getUserProfile",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data profil pengguna berhasil diambil.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token Tidak ada atau tidak valid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     * )
     */
    public function show()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $user->load('experiences');

        $user->makeHidden(['otp_code', 'otp_expires_at', 'password']);

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'message' => 'Data profil berhasil diambil.',
            'data' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/profile",
     *     summary="Memperbarui profil pengguna",
     *     description="Memperbarui data profil pengguna yang terautentikasi. Foto profil dapat diunggah dan informasi lain dapat diperbarui.",
     *     operationId="updateUserProfile",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data profil pengguna yang ingin diperbarui.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"name", "major", "year_generation", "job", "location", "province", "marital_status", "dependents", "phone_number", "lat", "lng"},
     *                     @OA\Property(property="name", type="string", example="Nama Pengguna Baru"),
     *                     @OA\Property(property="major", type="string", example="Akuntansi"),
     *                     @OA\Property(property="year_generation", type="string", example="2021"),
     *                     @OA\Property(property="job", type="string", example="Admin BUMN"),
     *                     @OA\Property(property="phone_number", type="string", example="+6281234567890"),
     *                     @OA\Property(property="location", type="string", example="Bandung"),
     *                     @OA\Property(property="province", type="string", example="Jawa Barat"),
     *                     @OA\Property(property="lat", type="string", example="-6.955867"),
     *                     @OA\Property(property="lng", type="string", example="107.615995"),
     *                     @OA\Property(property="marital_status", type="string", example="Married"),
     *                     @OA\Property(property="dependents", type="integer", example=2),
     *                     @OA\Property(
     *                         property="photo",
     *                         type="string",
     *                         format="binary",
     *                         nullable=true,
     *                         description="Foto profil pengguna"
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil pengguna berhasil diperbarui.",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Daftar kesalahan validasi"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token Tidak ada atau tidak valid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     * )
     */
    public function update(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'major' => 'required|string|max:100',
            'year_generation' => 'required|string|max:10',
            'job' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'province' => 'required|string|max:150',
            'lat' => 'nullable|string',
            'lng' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'marital_status' => 'required|string|max:20',
            'dependents' => 'required|integer|min:0',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $request->only([
            'name',
            'major',
            'year_generation',
            'job',
            'location',
            'province',
            'lat',
            'lng',
            'marital_status',
            'dependents',
            'phone_number',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('uploads/profile_pictures');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $data['photo'] = url('uploads/profile_pictures/' . $filename);
        }

        $user->update($data);

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user->makeHidden(['password', 'otp_code', 'otp_expires_at']),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/profile-experience",
     *     summary="Tambah atau update pengalaman kerja user",
     *     description="Gunakan endpoint ini untuk menambahkan atau mengupdate pengalaman kerja. 
     *                  Untuk **menambahkan data baru**, **jangan sertakan `experience_id`**. 
     *                  Untuk **mengupdate data**, **sertakan `experience_id`** berdasarkan data yang sudah ada pada profile pengguna",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="experiences",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="experience_id", type="string", example="experience-uuid-123", description="Wajib diisi saat update, kosongkan saat create"),
     *                     @OA\Property(property="company_name", type="string", example="PT Maju Mundur", description="Nama perusahaan tempat bekerja"),
     *                     @OA\Property(property="company_category", type="string", example="Teknologi", description="Kategori perusahaan"),
     *                     @OA\Property(property="position", type="string", example="Software Engineer", description="Jabatan yang dipegang"),
     *                     @OA\Property(property="start_year", type="integer", example=2020, description="Tahun mulai bekerja"),
     *                     @OA\Property(property="end_year", type="integer", example=2023, description="Tahun selesai bekerja (boleh kosong jika masih aktif)"),
     *                     @OA\Property(property="description", type="string", example="Bertanggung jawab atas pengembangan aplikasi mobile.", description="Deskripsi pengalaman kerja"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pengalaman kerja berhasil diperbarui.",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal.",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=400),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *    @OA\Response(
     *         response=401,
     *         description="Token Tidak ada atau tidak valid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     * )
     */
    public function updateExperiences(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'experiences' => 'nullable|array',
            'experiences.*.company_name' => 'required|string|max:255',
            'experiences.*.company_category' => 'required|string|max:100',
            'experiences.*.position' => 'required|string|max:100',
            'experiences.*.start_year' => 'required|integer',
            'experiences.*.end_year' => 'nullable|integer',
            'experiences.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->has('experiences')) {
            foreach ($request->experiences as $experience) {
                $experience['user_id'] = $user->user_id;

                if (empty($experience['experience_id'])) {
                    UserExperience::create($experience);
                } else {
                    $userExperience = UserExperience::find($experience['experience_id']);
                    if ($userExperience) {
                        $userExperience->update($experience);
                    }
                }
            }
        }

        $userWithExperiences = $user->load('experiences');

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'message' => 'Pengalaman kerja berhasil diperbarui.',
            'data' => $userWithExperiences,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/profile-experience/{experience_id}",
     *     summary="Hapus pengalaman kerja berdasarkan ID",
     *     description="Menghapus satu data pengalaman kerja berdasarkan ID yang dimiliki oleh user yang sedang login.",
     *     tags={"Profile Pengguna"},
     *     security={{"bearerAuth":{}}},
     *     operationId="deleteExperienceById",
     *     @OA\Parameter(
     *         name="experience_id",
     *         in="path",
     *         description="ID dari pengalaman kerja yang ingin dihapus",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pengalaman kerja berhasil dihapus",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pengalaman kerja tidak ditemukan",
     *     )
     * )
     */
    public function deleteExperienceById($experience_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $experience = UserExperience::where('experience_id', $experience_id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$experience) {
            return ApiResponse::error(
                'Pengalaman kerja tidak ditemukan.',
                ['experience_id' => ['ID Pengalaman bukan milik pengguna.']],
                404
            );
        }

        $experience->delete();

        return ApiResponse::success('Pengalaman kerja berhasil dihapus.');
    }
}
