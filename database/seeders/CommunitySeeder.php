<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CommunitySeeder extends Seeder
{
    public function run()
    {

        $categories = DB::table('community_categories')->pluck('category_id')->toArray();

        $communities = [
            [
                'name' => 'Komunitas Sepeda Santai',
                'description' => 'Komunitas sepeda santai yang suka aktif di jam pulang kerja dan weekend. Untuk semua alumni yang punya hobi olahraga sepeda boleh gabung di komunitas kita ya, seluruh informasi mengenai acaranya nanti di informasikan di dalam postingan grup komunitas ini. Terima kasih. Salah Sepedah!',
            ],
            [
                'name' => 'Pecinta Catur',
                'description' => 'Tempat berkumpulnya alumni yang suka bermain catur, baik yang hobi santai maupun yang sering ikut turnamen. Kita rutin adakan sparring online dan offline!',
            ],
            [
                'name' => 'Sunmori Bareng',
                'description' => 'Sunmori alias Sunday Morning Ride bareng alumni lintas angkatan. Start dari tempat yang udah disepakati, dan finish di tempat makan bareng. Santai aja, nggak harus motor gede!',
            ],
            [
                'name' => 'Bisnis Kuliner UMKM',
                'description' => 'Komunitas ini cocok buat kamu yang sedang atau ingin memulai bisnis kuliner. Sharing resep, strategi marketing, dan tips jualan setiap minggu!',
            ],
            [
                'name' => 'Musik Tradisional Angklung',
                'description' => 'Yuk lestarikan budaya! Komunitas ini terbuka untuk semua alumni yang ingin belajar, memainkan, atau sekadar menikmati musik tradisional angklung.',
            ],
        ];

        $user_ids = [
            'user-2e32e555-7947-4c59-82f5-14f97482dfe2',
            'user-483ab8d7-facf-4141-a20e-00489d91cc71',
        ];

        foreach ($communities as $community) {
        
            $category_id = $categories[array_rand($categories)];

            $community_id = 'comun-' . Str::uuid();
            DB::table('communities')->insert([
                'community_id' => $community_id,
                'name' => $community['name'],
                'description' => $community['description'],
                'category_id' => $category_id,
                'created_dt' => Carbon::now(),
            ]);

            for ($i = 0; $i < 2; $i++) {
                DB::table('community_user')->insert([
                    'community_id' => $community_id,
                    'user_id' => $user_ids[$i % 2],
                    'joined_dt' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }

            for ($i = 0; $i < 3; $i++) {
                DB::table('discussion')->insert([
                    'discus_id' => 'discus-' . Str::uuid(),
                    'community_id' => $community_id,
                    'user_id' => $user_ids[$i % 2],
                    'title' => 'Diskusi #' . ($i + 1) . ' di ' . $community['name'],
                    'content' => 'Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "' . $community['name'] . '". Ada yang pernah mengalami hal menarik?',
                    'created_dt' => Carbon::now()->subDays(rand(0, 20)),
                    'view_cnt' => rand(5, 150),
                    'like_cnt' => rand(0, 30),
                    'comment_cnt' => rand(0, 10),
                ]);
            }
        }
    }
}
