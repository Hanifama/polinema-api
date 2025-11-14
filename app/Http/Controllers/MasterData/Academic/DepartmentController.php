<?php

namespace App\Http\Controllers\MasterData\Academic;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{

    // /**
    //  * @OA\Get(
    //  *     path="/departments",
    //  *     summary="Ambil daftar semua departemen beserta programnya",
    //  *     description="Endpoint ini akan mengembalikan daftar seluruh departemen. Setiap departemen memiliki relasi one-to-many ke program studi (programs), yang akan disertakan di dalam objek department.",
    //  *     tags={"Master Data"},
    //  *     operationId="listDepartemen",
    //  *     security={{"bearerAuth":{}}},
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Berhasil mengambil daftar departemen"
    //  *     )
    //  * )
    //  */
    public function index()
    {
        $departments = Department::with('programs')->get();
        return ApiResponse::success('Data departemen berhasil dimuat.', $departments);
    }

    // /**
    //  * @OA\Get(
    //  *     path="/departments/{department_id}",
    //  *     summary="Ambil detail departemen berdasarkan ID beserta programnya",
    //  *     description="Endpoint ini akan mengembalikan detail dari satu departemen berdasarkan ID-nya. Objek department akan menyertakan data program studi (programs) sebagai relasi one-to-many.",
    //  *     tags={"Master Data"},
    //  *     operationId="detailDepartemen",
    //  *     security={{"bearerAuth":{}}},
    //  *     @OA\Parameter(
    //  *         name="department_id",
    //  *         in="path",
    //  *         description="ID departemen yang ingin diambil datanya",
    //  *         required=true,
    //  *         @OA\Schema(type="string")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Berhasil mengambil detail departemen"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="Departemen tidak ditemukan"
    //  *     )
    //  * )
    //  */
    public function show($department_id)
    {
        $department = Department::with('programs')->find($department_id);

        if (!$department) {
            return ApiResponse::error('Departemen tidak ditemukan', 404);
        }

        return ApiResponse::success('Detail departemen berhasil dimuat.', $department);
    }
}
