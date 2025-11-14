<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Acara Reuni Alumni Polinema 2025',
                'slug' => 'acara-reuni-alumni-polinema-2025',
                'content' => 'Mari bergabung dalam acara reuni alumni Polinema 2025 yang akan diselenggarakan di kampus Polinema. Ini adalah kesempatan emas untuk bertemu teman lama dan memperluas jaringan.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Lowongan Kerja untuk Alumni Polinema',
                'slug' => 'lowongan-kerja-alumni-polinema',
                'content' => 'Dibuka lowongan kerja untuk alumni Polinema di berbagai bidang, dari teknik hingga manajerial. Temukan posisi yang sesuai dengan keahlianmu dan bergabung dengan perusahaan terkemuka.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Berita Terbaru: Polinema Meningkatkan Fasilitas Kampus',
                'slug' => 'berita-terbaru-polinema-meningkatkan-fasilitas-kampus',
                'content' => 'Polinema baru saja menyelesaikan renovasi fasilitas kampus yang mencakup gedung perkuliahan baru, ruang penelitian, dan area olahraga. Ini adalah langkah besar untuk meningkatkan kualitas pendidikan bagi mahasiswa dan alumni.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Tips Karir untuk Alumni Polinema',
                'slug' => 'tips-karir-alumni-polinema',
                'content' => 'Bagi alumni yang sedang mencari pekerjaan atau memulai karir baru, berikut adalah beberapa tips dari alumni sukses Polinema yang bisa membantu mempercepat perjalanan karir Anda.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Pengumuman: Seminar tentang Teknologi Terbaru untuk Alumni',
                'slug' => 'seminar-teknologi-terbaru-alumni',
                'content' => 'Alumni Polinema diundang untuk mengikuti seminar tentang teknologi terbaru yang akan diselenggarakan bulan depan. Seminar ini akan menghadirkan berbagai pembicara dari industri teknologi terkemuka.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Webinar Pengembangan Karir bagi Alumni Polinema',
                'slug' => 'webinar-pengembangan-karir-alumni-polinema',
                'content' => 'Ikuti webinar pengembangan karir yang akan membahas peluang karir terbaru di dunia digital. Alumni Polinema dapat bergabung secara gratis.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Program Mentoring untuk Alumni Polinema',
                'slug' => 'program-mentoring-alumni-polinema',
                'content' => 'Program mentoring alumni Polinema kini dibuka. Alumni yang ingin berbagi pengalaman atau membutuhkan bimbingan bisa bergabung dalam program ini.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Alumni Polinema Berprestasi: Kisah Sukses di Dunia Kerja',
                'slug' => 'alumni-polinema-berprestasi',
                'content' => 'Beberapa alumni Polinema telah mencapai kesuksesan luar biasa di dunia kerja. Artikel ini akan membahas kisah-kisah inspiratif mereka.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Update: Program Beasiswa untuk Alumni Polinema',
                'slug' => 'update-program-beasiswa-alumni-polinema',
                'content' => 'Dapatkan informasi terbaru mengenai program beasiswa bagi alumni Polinema yang ingin melanjutkan studi ke jenjang yang lebih tinggi.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
            [
                'title' => 'Perkembangan Teknologi di Polinema: Apa yang Baru?',
                'slug' => 'perkembangan-teknologi-di-polinema',
                'content' => 'Polinema terus berinovasi dengan memperkenalkan teknologi terbaru di fasilitas kampus. Berikut adalah update terkini mengenai perkembangan tersebut.',
                'author' => 'Hi Polinema',
                'published_by' => 'Admin',
            ],
        ];

        foreach ($articles as $article) {
            Article::create([
                'article_id' => 'article-' . Str::uuid(),
                'title' => $article['title'],
                'slug' => $article['slug'],
                'content' => $article['content'],
                'author' => $article['author'],
                'published_dt' => now(),
                'published_by' => $article['published_by'],
                'image'        => 'https://api-polinema.webview.cloud/events/article.png', 
            ]);
        }
    }
}
