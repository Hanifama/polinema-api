<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommunityCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'category_id' => 'categ-' . Str::uuid(),
                'name' => 'Hobi',
                'photo' => 'https://api-polinema.webview.cloud/community/komunitas.png',
                'created_dt' => now(),
            ],
            [
                'category_id' => 'categ-' . Str::uuid(),
                'name' => 'Seni',
                'photo' => 'https://api-polinema.webview.cloud/community/komunitas.png',
                'created_dt' => now(),
            ],
            [
                'category_id' => 'categ-' . Str::uuid(),
                'name' => 'Bisnis',
                'photo' => 'https://api-polinema.webview.cloud/community/komunitas.png',
                'created_dt' => now(),
            ],
            [
                'category_id' => 'categ-' . Str::uuid(),
                'name' => 'Politik',
                'photo' => 'https://api-polinema.webview.cloud/community/komunitas.png',
                'created_dt' => now(),
            ],
        ];

        DB::table('community_categories')->insert($categories);
    }
}
