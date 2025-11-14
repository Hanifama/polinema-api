<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BannerTenantSeeder extends Seeder
{
    public function run(): void
    {
        $image = 'https://api-polinema.webview.cloud/uploads/banners/tenants/dfasdfsarfewffasdfasdefwgds.png';

        for ($i = 1; $i <= 4; $i++) {
            DB::table('banner_tenant')->insert([
                'banner_id' => 'banner-tenant-' . Str::uuid(),
                'image' => $image,
                'date_from' => now()->subDays(rand(1, 10)),
                'date_to' => now()->addDays(rand(5, 15)),
                'status' => 'active',
            ]);
        }
    }
}
