<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $imageUrl = 'https://api-polinema.webview.cloud/uploads/banners/spotlights/berlian.png';

        Banner::create([
            'banner_id' => 'banner-' . Str::uuid(),
            'image' => $imageUrl,
            'date_from' => '2025-06-14',
            'date_to' => '2025-08-01',
            'status' => 'active',
            'content' => 'Reuni Akbar Alumni Polinema 2025 — Mari perkuat jejaring, kenang masa indah, dan bangun masa depan bersama.'
        ]);

        Banner::create([
            'banner_id' => 'banner-' . Str::uuid(),
            'image' => $imageUrl,
            'date_from' => '2025-06-14',
            'date_to' => '2025-07-31',
            'status' => 'active',
            'content' => 'Karier Alumni: Peluang kerja eksklusif bagi alumni Polinema di perusahaan mitra strategis.'
        ]);

        Banner::create([
            'banner_id' => 'banner-' . Str::uuid(),
            'image' => $imageUrl,
            'date_from' => '2025-06-14',
            'date_to' => '2025-07-20',
            'status' => 'active',
            'content' => 'Cerita Sukses Alumni: Dari kampus Polinema hingga menjadi pemimpin startup nasional.'
        ]);
    }
}
