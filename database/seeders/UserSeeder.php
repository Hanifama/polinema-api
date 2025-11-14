<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $defaultAttributes = [
            'password' => Hash::make('password123'),
            'status' => 'active',
            'role' => 'user',
            'photo' => 'https://api-polinema.webview.cloud/uploads/profile_pictures/1744464238_user-perempuan.jpeg',
            'otp_code' => null,
            'otp_expires_at' => null,
            'created_dt' => Carbon::now(),
        ];

        $usersData = [
            // 6 orang Bandung
            [
                'name' => 'Hi Polinema',
                'email' => 'testingakunajalah@gmail.com',
                'phone_number' => '+628123456789',
                'major' => 'Teknik Komputer',
                'year_generation' => '2020',
                'job' => 'Freelancer',
                'location' => 'Bandung',
                'lat' => -6.921967,
                'lng' => 107.606979,
                'marital_status' => 'menikah',
                'dependents' => 3,
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@example.com',
                'phone_number' => '+6281222222222',
                'major' => 'Teknik Informatika',
                'year_generation' => '2020',
                'job' => 'Software Engineer',
                'location' => 'Alun-Alun Bandung',
                'lat' => -6.921967,
                'lng' => 107.606979,
                'marital_status' => 'menikah',
                'dependents' => 3,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone_number' => '+6281333333333',
                'major' => 'Sistem Informasi',
                'year_generation' => '2019',
                'job' => 'Data Analyst',
                'location' => 'Braga, Bandung',
                'lat' => -6.916469,
                'lng' => 107.609558,
                'marital_status' => 'menikah',
                'dependents' => 3,
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra@example.com',
                'phone_number' => '+6281444444444',
                'major' => 'Teknik Komputer',
                'year_generation' => '2021',
                'job' => 'UI/UX Designer',
                'location' => 'Balai Kota Bandung',
                'lat' => -6.912755,
                'lng' => 107.609146,
                'marital_status' => 'menikah',
                'dependents' => 2,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'phone_number' => '+6281555555555',
                'major' => 'Manajemen',
                'year_generation' => '2022',
                'job' => 'Project Manager',
                'location' => 'BIP, Bandung',
                'lat' => -6.900496,
                'lng' => 107.618347,
                'marital_status' => 'belum menikah',
                'dependents' => 0,
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko@example.com',
                'phone_number' => '+6281666666666',
                'major' => 'Teknik Elektro',
                'year_generation' => '2023',
                'job' => 'DevOps Engineer',
                'location' => 'Pasar Baru, Bandung',
                'lat' => -6.919632,
                'lng' => 107.604027,
                'marital_status' => 'belum menikah',
                'dependents' => 2,
            ],

            // 5 orang Malang
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri@example.com',
                'phone_number' => '+6281777777777',
                'major' => 'Akuntansi',
                'year_generation' => '2018',
                'job' => 'Accountant',
                'location' => 'Alun-Alun Malang',
                'lat' => -7.981894,
                'lng' => 112.631378,
                'marital_status' => 'belum menikah',
                'dependents' => 1,
            ],
            [
                'name' => 'Gilang Saputra',
                'email' => 'gilang@example.com',
                'phone_number' => '+6281888888888',
                'major' => 'Teknik Sipil',
                'year_generation' => '2017',
                'job' => 'Civil Engineer',
                'location' => 'Ijen Boulevard, Malang',
                'lat' => -7.975018,
                'lng' => 112.632599,
                'marital_status' => 'belum menikah',
                'dependents' => 2,
            ],
            [
                'name' => 'Hana Puspita',
                'email' => 'hana@example.com',
                'phone_number' => '+6281999999999',
                'major' => 'Psikologi',
                'year_generation' => '2020',
                'job' => 'HR Specialist',
                'location' => 'Tugu Malang',
                'lat' => -7.979070,
                'lng' => 112.630630,
                'marital_status' => 'belum menikah',
                'dependents' => 4,
            ],
            [
                'name' => 'Ivan Nugraha',
                'email' => 'ivan@example.com',
                'phone_number' => '+6282000000000',
                'major' => 'Teknik Mesin',
                'year_generation' => '2016',
                'job' => 'Mechanical Engineer',
                'location' => 'Matos, Malang',
                'lat' => -7.948471,
                'lng' => 112.615845,
                'marital_status' => 'belum menikah',
                'dependents' => 4,
            ],
            [
                'name' => 'Jasmine Zahra',
                'email' => 'jasmine@example.com',
                'phone_number' => '+6282111111111',
                'major' => 'Desain Komunikasi Visual',
                'year_generation' => '2021',
                'job' => 'Graphic Designer',
                'location' => 'Polinema, Malang',
                'lat' => -7.946555,
                'lng' => 112.615052,
                'marital_status' => 'belum menikah',
                'dependents' => 4,
            ],
        ];

        $users = collect($usersData)->map(function ($user) use ($defaultAttributes) {
            return array_merge($defaultAttributes, $user, [
                'user_id' => 'user-' . (string) Str::uuid(),
            ]);
        })->toArray();

        User::insert($users);
    }
}
