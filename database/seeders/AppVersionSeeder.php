<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppVersion;
use Illuminate\Support\Carbon;

class AppVersionSeeder extends Seeder
{
    public function run(): void
    {
        AppVersion::create([
            'version_code' => '1.0.0',
            'platform' => 'android',
            'version_name' => '1.0.0',
            'version_description' => 'first launching',
            'is_latest' => true,
            'is_allowed' => true,
            'released_date' => Carbon::parse('2025-06-15 10:00:00'),
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);
         AppVersion::create([
            'version_code' => '1.0.0',
            'platform' => 'ios',
            'version_name' => '1.0.0',
            'version_description' => 'first launching',
            'is_latest' => true,
            'is_allowed' => true,
            'released_date' => Carbon::parse('2025-06-15 10:00:00'),
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);
    }
}
