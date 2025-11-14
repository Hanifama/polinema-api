<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VoucherCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Diskon',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/diskon.png',
                'description' => 'Voucher untuk potongan harga langsung pada pembelian produk.',
            ],
            [
                'name' => 'Buy 1 Get 1',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/buy1get1.png',
                'description' => 'Dapatkan produk kedua secara gratis dengan pembelian produk pertama.',
            ],
            [
                'name' => 'Free Breakfast',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/freebreakfast.png',
                'description' => 'Nikmati sarapan gratis dengan voucher ini.',
            ],
            [
                'name' => 'Free Drink',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/freedrink.png',
                'description' => 'Dapatkan minuman gratis saat menggunakan voucher ini.',
            ],
            [
                'name' => 'Menu Paket',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/menupaket.png',
                'description' => 'Promo paket makanan dengan harga spesial.',
            ],
            [
                'name' => 'Promo Maksimal',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/promomaksimal.png',
                'description' => 'Dapatkan promo dengan potongan maksimal untuk pembelian tertentu.',
            ],
            [
                'name' => 'Alumni Hemat',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/alumnihemat.png',
                'description' => 'Diskon khusus untuk alumni sebagai bentuk apresiasi.',
            ],
            [
                'name' => 'Promo Solusi',
                'icon' => 'https://api-polinema.webview.cloud/vochers/icon/promisisolusi.png',
                'description' => 'Penawaran promo spesial sebagai solusi kebutuhan Anda.',
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('voucher_categories')->insert([
                'vocat_id' => 'vocat-' . Str::uuid(),
                'name' => $cat['name'],
                'icon' => $cat['icon'],
                'description' => $cat['description'],
                'created_dt' => now(),
            ]);
        }
    }
}
