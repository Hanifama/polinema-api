<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Helpers\ApiResponse;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/events",
     *     summary="Mendapatkan semua acara",
     *     description="Mengambil daftar semua acara yang tersedia.",
     *     tags={"Acara"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Acara berhasil diambil"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ada acara ditemukan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized - Tidak terautentikasi"
     *     )
     * )
     */
    public function getAllEvents()
    {
        $events = Event::all();

        if ($events->isEmpty()) {
            return ApiResponse::error('Acara belum dibuat.', [], 404);
        }

        return ApiResponse::success('Acara berhasil dimuat.', $events);
    }

    /**
     * @OA\Get(
     *     path="/events/{event_id}",
     *     summary="Mendapatkan detail acara berdasarkan ID",
     *     description="Mengambil acara berdasarkan event_id.",
     *     tags={"Acara"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Acara berhasil diambil"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Acara tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error: Unauthorized - Tidak terautentikasi"
     *     )
     * )
     */
    public function getEventDetail(string $event_id)
    {
        $event = Event::find($event_id);

        if (!$event) {
            return ApiResponse::error('Acara tidak ditemukan.', [], 404);
        }

        return ApiResponse::success('Detail acara berhasil diambil.', $event);
    }
}
