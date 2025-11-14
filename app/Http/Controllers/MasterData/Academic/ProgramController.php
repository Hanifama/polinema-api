<?php

namespace App\Http\Controllers\MasterData\Academic;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Program;


class ProgramController extends Controller
{

    /**
     * @OA\Get(
     *     path="/master-data/programs",
     *     summary="Ambil daftar semua jurusan beserta departemennya",
     *     description="Endpoint ini akan mengembalikan daftar seluruh jurusan program studi. Setiap program memiliki relasi ke satu departemen (department). Data department disertakan di dalam objek program.",
     *     tags={"Master Data"},
     *     operationId="listProgram",
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar program"
     *     )
     * )
     */
    public function index()
    {
        $programs = Program::with('department')->get();

        return ApiResponse::success('Data program berhasil dimuat.', $programs);
    }

    /**
     * @OA\Get(
     *     path="/master-data/programs/{program_id}",
     *     summary="Ambil detail jurusan berdasarkan ID beserta departemennya",
     *     description="Endpoint ini akan mengembalikan detail dari satu jurusan berdasarkan ID-nya. Objek program akan menyertakan data departemen (department) sebagai relasi.",
     *     tags={"Master Data"},
     *     operationId="detailProgram",
     *     @OA\Parameter(
     *         name="program_id",
     *         in="path",
     *         description="ID program yang ingin diambil datanya",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil detail program"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Program tidak ditemukan"
     *     )
     * )
     */
    public function show($program_id)
    {
        $program = Program::with('department')->find($program_id);

        if (!$program) {
            return ApiResponse::error('Program tidak ditemukan', 404);
        }

        return ApiResponse::success('Detail program berhasil dimuat.', $program);
    }
}
