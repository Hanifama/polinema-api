<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('tenant_categories')->get();
        $tenantNames = [
            'Ayam Geprek Juara',
            'Bakso Mang Udin',
            'Sate Padang Uni',
            'Kopi Senja',
            'Martabak Bang Jali',
            'Mie Ayam Dower',
            'Dimsum QQ',
            'Nasi Goreng Gila',
            'Es Teh Manis Boba',
            'Tahu Crispy Kang Ronny'
        ];

        $userIds = [
            'usr-2e32e555-7947-4c59-82f5-14f97482dfe2',
            'usr-4c7ae0df-1d78-4a21-9315-2eb25bedcc6a',
            'usr-025620e5-5bb8-4fba-9a8c-69b1ee384973',
            'usr-51872d15-1fb7-468d-b4ed-255c1c82fbd2',
            'usr-1c3e2b57-4442-4d8f-ac39-d1f99dc84236'
        ];

        for ($i = 0; $i < count($tenantNames); $i++) {
            $name = $tenantNames[$i];
            $cat = $categories[$i % count($categories)];
            $userIndex = intdiv($i, 2); 

            $randomLat = -6.914744 + (rand(-300, 300) / 10000);
            $randomLng = 107.609810 + (rand(-300, 300) / 10000);

            DB::table('tenants')->insert([
                'tenant_id' => (string) Str::uuid(),
                'tencat_id' => $cat->tencat_id,
                'name' => $name,
                'banner' => Str::slug($name) . "_banner.jpg",
                'address' => "Jl. Contoh No. " . ($i + 1) . ", Bandung",
                'about' => "Nikmati hidangan khas dari " . $name,
                'image_1' => 'https://api-polinema.webview.cloud/vochers/banner/banner.jpg',
                'image_2' => 'https://api-polinema.webview.cloud/vochers/banner/banner.jpg',
                'image_3' => 'https://api-polinema.webview.cloud/vochers/banner/banner.jpg',
                'image_4' => 'https://api-polinema.webview.cloud/vochers/banner/banner.jpg',
                'lat' => $randomLat,
                'lng' => $randomLng,
                'status' => 'active',
                'created_dt' => Carbon::now(),
                'created_by' => $userIds[$userIndex % count($userIds)],
            ]);
        }
    }
}
