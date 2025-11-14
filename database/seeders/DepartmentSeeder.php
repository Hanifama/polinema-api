<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            ['department_id' => 'TEKNIK', 'name' => 'Teknik', 'created_dt' => now()],
            ['department_id' => 'TI', 'name' => 'Teknologi Informasi', 'created_dt' => now()],
            ['department_id' => 'AKUNTANSI', 'name' => 'Akuntansi', 'created_dt' => now()],
            ['department_id' => 'ADM_NIAGA', 'name' => 'Administrasi Niaga', 'created_dt' => now()],
        ]);
    }
}
