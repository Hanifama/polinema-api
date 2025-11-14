<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SystemMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_master')->insert([
            [
                'category' => 'app_information',
                'sub_category' => 'sponsor_by',
                'key' => 'logo_hipolinema',
                'value' => 'https://api-polinema.webview.cloud/uploads/sponsorby/hipolinema.png',
                'description' => 'HIPolinema',
                'status' => 1,
                'created_dt' => Carbon::now(),
                'created_by' => 'system',
            ],
            [
                'category' => 'app_information',
                'sub_category' => 'sponsor_by',
                'key' => 'logo_ikapolinema',
                'value' => 'https://api-polinema.webview.cloud/uploads/sponsorby/ikapolinema.png',
                'description' => 'IKA Polinema',
                'status' => 1,
                'created_dt' => Carbon::now(),
                'created_by' => 'system',
            ],
            [
                'category' => 'app_information',
                'sub_category' => 'about',
                'key' => 'about_app',
                'value' => '<p><strong>HI!Polinema</strong> adalah aplikasi resmi alumni Politeknik Negeri Malang untuk membangun koneksi, kolaborasi, dan kontribusi bersama antar alumni.</p>',
                'description' => 'Tentang Aplikasi HI!Polinema',
            ],
            [
                'category' => 'app_information',
                'sub_category' => 'terms',
                'key' => 'terms_conditions',
                'value' => '<p>Dengan menggunakan aplikasi ini, Anda setuju dengan syarat dan ketentuan yang berlaku.</p>',
                'description' => 'Syarat & Ketentuan',
            ],
            [
                'category' => 'info',
                'sub_category' => 'privacy',
                'key' => 'privacy_policy',
                'value' => '<p>Kami menjaga privasi Anda dengan baik. Data Anda tidak akan dibagikan tanpa izin.</p>',
                'description' => 'Kebijakan Privasi',
            ],
            [
                'category' => 'app_information',
                'sub_category' => 'faqs',
                'key' => 'faqs',
                'value' => '<ul><li><strong>Q:</strong> Siapa saja yang bisa menggunakan aplikasi ini?<br><strong>A:</strong> Seluruh alumni Polinema.</li></ul>',
                'description' => 'FAQs',
            ],
        ]);
    }
}
