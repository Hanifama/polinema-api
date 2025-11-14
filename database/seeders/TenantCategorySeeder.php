<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Resto',
            'Mini Market',
            'Cafe',
            'Penginapan',
            'Jasa',
        ];

        foreach ($categories as $name) {
            DB::table('tenant_categories')->insert([
                'tencat_id' => (string) Str::uuid(),
                'name' => $name,
                'icon' => strtolower(str_replace([' ', '&'], ['_', ''], $name)) . '.png',
                'status' => 'active',
            ]);
        }
    }
}
