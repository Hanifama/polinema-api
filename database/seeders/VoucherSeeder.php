<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('voucher_categories')->get()->keyBy('name');

        $tenants = DB::table('tenants')->get();

        $bannerUrl = 'https://api-polinema.webview.cloud/vochers/banner/banner.jpg';

        foreach ($tenants as $i => $tenant) {
            $categoryName = $categories->keys()->toArray()[$i % $categories->count()];
            $category = $categories->get($categoryName);

            DB::table('vouchers')->insert([
                'voucher_id' => 'voucher-' . Str::uuid(),
                'tenant_id' => $tenant->tenant_id,
                'vocat_id' => $category->vocat_id,
                'title' => $category->name,
                'description' => "Gunakan voucher '{$category->name}' sekarang juga!",
                'banner_1' => $bannerUrl,
                'banner_2' => $bannerUrl,
                'banner_3' => $bannerUrl,
                'banner_4' => $bannerUrl,
                'discount_type' => 'percentage',
                'discount_value' => rand(10, 50),
                'minimum_amount' => rand(50, 200),
                'maximum_discount' => rand(30, 100),
                'start_dt' => Carbon::now(),
                'end_dt' => Carbon::now()->addDays(rand(15, 60)),
                'is_claimed' => false,
                'quota' => 100,
                'used' => 0,
                'created_dt' => Carbon::now(),
                'status' => 'active',
            ]);
        }
    }
}
