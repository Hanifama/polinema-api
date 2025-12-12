<?php

namespace App\Http\Controllers\ReportUser;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use App\Models\UserReport;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportUserController extends Controller
{
    /* ============================================================
     *                    CATEGORY SECTION
     * ============================================================ */

    private function generateCode($name)
    {
        $clean = preg_replace('/[^A-Za-z0-9 ]/', '', $name);
        return strtoupper(str_replace(' ', '_', $clean));
    }

    /**
     * @OA\Get(
     *     path="/report-categories",
     *     summary="Ambil semua kategori report",
     *     tags={"Report - Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Berhasil mengambil kategori")
     * )
     */
    public function categoryIndex()
    {
        $categories = ReportCategory::orderBy('name')->get();
        return ApiResponse::success('Berhasil mengambil kategori.', $categories);
    }

    /**
     * @OA\Post(
     *     path="/report-categories",
     *     summary="Buat kategori report baru",
     *     tags={"Report - Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Kategori berhasil dibuat")
     * )
     */
    public function categoryStore(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string'
        ]);

        $category = ReportCategory::create([
            'code'        => $this->generateCode($request->name),
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
            'created_dt'  => now(),
            'updated_dt'  => now(),
        ]);

        return ApiResponse::success('Kategori berhasil dibuat.', $category, 201);
    }

    /**
     * @OA\Get(
     *     path="/report-categories/{code}",
     *     summary="Detail kategori report",
     *     tags={"Report - Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Kode kategori (ex: SPAM, ABUSIVE_BEHAVIOR)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Berhasil"),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function categoryShow($code)
    {
        $category = ReportCategory::where('code', $code)->first();

        if (!$category) {
            return ApiResponse::error('Kategori tidak ditemukan.', [], 404);
        }

        return ApiResponse::success('Kategori ditemukan.', $category);
    }



    /* ============================================================
     *                    USER REPORT SECTION
     * ============================================================ */

    /**
     * @OA\Get(
     *     path="/user-reports",
     *     summary="Ambil semua report milik user",
     *     tags={"User Report"},
     *     security={{"bearerAuth": {}}},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function reportIndex()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $reports = UserReport::with('category')
            ->where('reporter_user_id', $user->user_id)
            ->orderBy('created_dt', 'desc')
            ->get();

        return ApiResponse::success('Berhasil mengambil laporan.', $reports);
    }

    /**
     * @OA\Post(
     *     path="/user-reports",
     *     summary="Buat laporan user",
     *     tags={"User Report"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reported_user_id", "category_code"},
     *             @OA\Property(property="reported_user_id", type="string", example="user-e27a6eba-e908-4f5f-9a19-a753cebe6e93"),
     *             @OA\Property(property="category_code", type="string", example="SPAM"),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Berhasil")
     * )
     */
    public function reportStore(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validated = $request->validate([
            'reported_user_id' => 'required|string',
            'category_code'      => 'required|string|exists:report_categories,code',
            'description'      => 'nullable|string',
        ]);

        $validated['reporter_user_id'] = $user->user_id;

        $report = UserReport::create($validated);

        return ApiResponse::success('Laporan berhasil dibuat.', $report, 201);
    }

    /**
     * @OA\Get(
     *     path="/user-reports/{id}",
     *     summary="Detail report user",
     *     tags={"User Report"},
     *     security={{"bearerAuth": {}}},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Berhasil"),
     *     @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function reportShow($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $report = UserReport::with('category')
            ->where('reporter_user_id', $user->id)
            ->find($id);

        if (!$report) {
            return ApiResponse::error('Report tidak ditemukan.', [], 404);
        }

        return ApiResponse::success('Berhasil.', $report);
    }
}
