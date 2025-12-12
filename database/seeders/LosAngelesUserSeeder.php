<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class LosAngelesUserSeeder extends Seeder
{
    public function run()
    {
        $default = [
            'password'        => Hash::make('password123'),
            'status'          => 'active',
            'role'            => 'user',
            'photo'           => 'https://api-polinema.webview.cloud/uploads/profile_pictures/1744464238_user-perempuan.jpeg',
            'otp_code'        => null,
            'otp_expires_at'  => null,
            'created_dt'      => Carbon::now(),
        ];

        // Dummy Koordinat sekitar jalan yang kamu sebutkan
        $laLocations = [
            [
                'name' => 'Jessica Smith',
                'email' => 'jessica.la@example.com',
                'phone_number' => '+12130001111',
                'major' => 'Computer Science',
                'year_generation' => 2019,
                'job' => 'Product Designer',
                'location' => 'Jacklin Rd, Los Angeles',
                'lat' => 34.010871,
                'lng' => -118.165977,
                'marital_status' => 'single',
                'dependents' => 0,
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.la@example.com',
                'phone_number' => '+12130002222',
                'major' => 'Information Technology',
                'year_generation' => 2018,
                'job' => 'Software Engineer',
                'location' => 'N Abel St, Los Angeles',
                'lat' => 34.011450,
                'lng' => -118.167420,
                'marital_status' => 'single',
                'dependents' => 0,
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophia.la@example.com',
                'phone_number' => '+12130003333',
                'major' => 'Graphic Design',
                'year_generation' => 2020,
                'job' => 'UI/UX Designer',
                'location' => 'S Abel St, Los Angeles',
                'lat' => 34.009500,
                'lng' => -118.167781,
                'marital_status' => 'single',
                'dependents' => 1,
            ],
            [
                'name' => 'Daniel Lee',
                'email' => 'daniel.la@example.com',
                'phone_number' => '+12130004444',
                'major' => 'Business',
                'year_generation' => 2017,
                'job' => 'Operations Manager',
                'location' => 'Hotetter Rd, Los Angeles',
                'lat' => 34.008320,
                'lng' => -118.169900,
                'marital_status' => 'married',
                'dependents' => 2,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.la@example.com',
                'phone_number' => '+12130005555',
                'major' => 'Marketing',
                'year_generation' => 2021,
                'job' => 'Social Media Specialist',
                'location' => 'Near Jacklin Rd, Los Angeles',
                'lat' => 34.011900,
                'lng' => -118.165500,
                'marital_status' => 'single',
                'dependents' => 0,
            ],
        ];

        foreach ($laLocations as $user) {
            User::create(array_merge($default, $user, [
                'user_id' => 'user-' . Str::uuid(),
            ]));
        }
    }
}
