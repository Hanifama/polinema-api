<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ReportCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'ABUSIVE_BEHAVIOR',
                'name' => 'Pelecehan / Abusive Behavior',
                'description' => '<p>Termasuk penghinaan, ancaman, intimidasi, pelecehan verbal, atau tindakan yang mengganggu pengguna lain.</p>',
            ],
            [
                'code' => 'VIOLENCE',
                'name' => 'Kekerasan',
                'description' => '<p>Konten atau perilaku yang mempromosikan kekerasan fisik, ancaman kekerasan, atau tindakan berbahaya lainnya.</p>',
            ],
            [
                'code' => 'ILLEGAL_ACTIVITIES',
                'name' => 'Aktivitas Ilegal',
                'description' => '<p>Kegiatan terlarang seperti penggunaan narkoba, jual beli ilegal, atau aktivitas kriminal lainnya.</p>',
            ],
            [
                'code' => 'BULLYING',
                'name' => 'Perundungan / Bullying',
                'description' => '<p>Tindakan merundung, mempermalukan, atau menyerang seseorang secara berulang.</p>',
            ],
            [
                'code' => 'HATE_SPEECH',
                'name' => 'Ujaran Kebencian',
                'description' => '<p>Konten yang menyerang kelompok berdasarkan ras, agama, gender, orientasi seksual, atau karakteristik lainnya.</p>',
            ],
            [
                'code' => 'SEXUAL_CONTENT',
                'name' => 'Konten Dewasa / Sexual Content',
                'description' => '<p>Konten yang bersifat pornografi, sugestif, atau eksplisit secara seksual.</p>',
            ],
            [
                'code' => 'SELF_HARM',
                'name' => 'Bunuh Diri / Melukai Diri',
                'description' => '<p>Konten yang mempromosikan atau menggambarkan tindakan bunuh diri, melukai diri sendiri, atau ajakan untuk melukai diri.</p>',
            ],
            [
                'code' => 'SPAM',
                'name' => 'Spam',
                'description' => '<p>Aktivitas berulang yang mengganggu seperti pesan massal, promosi tidak relevan, atau pola spam lainnya.</p>',
            ],
            [
                'code' => 'SCAM_FRAUD',
                'name' => 'Penipuan / Fraud',
                'description' => '<p>Segala bentuk penipuan, skema finansial palsu, atau upaya untuk mengelabui pengguna.</p>',
            ],
            [
                'code' => 'IMPERSONATION',
                'name' => 'Penyamaran / Impersonation',
                'description' => '<p>Akun yang meniru, memalsukan identitas, atau berpura-pura sebagai pengguna lain.</p>',
            ],
            [
                'code' => 'PRIVACY_VIOLATION',
                'name' => 'Pelanggaran Privasi',
                'description' => '<p>Membagikan data pribadi, foto tanpa izin, atau informasi sensitif tanpa persetujuan.</p>',
            ],
            [
                'code' => 'MISINFORMATION',
                'name' => 'Informasi Palsu / Hoax',
                'description' => '<p>Konten yang berisi informasi salah, menyesatkan, atau hoax yang dapat merugikan pengguna lain.</p>',
            ],
            [
                'code' => 'COPYRIGHT',
                'name' => 'Pelanggaran Hak Cipta',
                'description' => '<p>Penggunaan konten berhak cipta tanpa izin seperti musik, video, atau karya digital lainnya.</p>',
            ],
            [
                'code' => 'MINOR_SAFETY',
                'name' => 'Keamanan Anak',
                'description' => '<p>Konten atau perilaku yang membahayakan atau mengeksploitasi anak di bawah umur.</p>',
            ],
            [
                'code' => 'OTHER',
                'name' => 'Lainnya',
                'description' => '<p>Kategori laporan lainnya yang tidak termasuk dalam daftar di atas.</p>',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('report_categories')->insert([
                'code'       => $category['code'],
                'name'       => $category['name'],
                'description' => $category['description'],
                'is_active'  => true,
                'created_dt' => now(),
                'updated_dt' => now(),
            ]);
        }
    }
}
