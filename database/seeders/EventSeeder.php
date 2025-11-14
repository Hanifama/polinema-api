<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'event_name' => 'Job Fair Polinema 2025',
                'event_date' => '2025-05-10',
                'location' => 'Gedung Graha Polinema',
                'description' => 'Acara bursa kerja untuk alumni Polinema bekerja sama dengan perusahaan nasional.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Workshop Karir Digital',
                'event_date' => '2025-06-15',
                'location' => 'Aula Pertamina Polinema',
                'description' => 'Pelatihan tentang karir digital marketing dan IT.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Reuni Akbar Alumni 2020',
                'event_date' => '2025-07-20',
                'location' => 'Lapangan Utama Polinema',
                'description' => 'Reuni besar alumni angkatan 2020.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Seminar Kewirausahaan Alumni',
                'event_date' => '2025-08-05',
                'location' => 'Aula Gedung Sipil',
                'description' => 'Seminar membangun bisnis sendiri untuk alumni.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Pelatihan Public Speaking',
                'event_date' => '2025-09-12',
                'location' => 'Gedung Fasilkom Polinema',
                'description' => 'Pelatihan meningkatkan public speaking.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Polinema Alumni Awards',
                'event_date' => '2025-10-17',
                'location' => 'Gedung Graha Polinema',
                'description' => 'Penghargaan alumni berprestasi.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Sosialisasi Program Magang',
                'event_date' => '2025-11-05',
                'location' => 'Ruang Seminar Teknik',
                'description' => 'Program magang untuk alumni dan mahasiswa akhir.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Pelatihan UI/UX Design',
                'event_date' => '2025-12-08',
                'location' => 'Gedung Informatika Polinema',
                'description' => 'Workshop UI/UX Design.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Talkshow Karir di Startup',
                'event_date' => '2026-01-22',
                'location' => 'Aula Polinema',
                'description' => 'Talkshow alumni sukses di dunia startup.',
                'status' => 'aktif',
            ],
            [
                'event_name' => 'Pelatihan Bahasa Inggris Profesional',
                'event_date' => '2026-02-14',
                'location' => 'Laboratorium Bahasa Polinema',
                'description' => 'Pelatihan Bahasa Inggris profesional.',
                'status' => 'aktif',
            ],
        ];

        foreach ($events as $event) {
            DB::table('events')->insert([
                'event_id' => 'event-' . Str::uuid(),
                'event_name' => $event['event_name'],
                'event_date' => $event['event_date'],
                'location' => $event['location'],
                'description' => $event['description'],
                'photo' => 'https://api-polinema.webview.cloud/events/event.png',
                'status' => $event['status'],
                'created_dt' => Carbon::now(),
            ]);
        }
    }
}
