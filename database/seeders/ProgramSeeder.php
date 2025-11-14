<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        DB::table('programs')->insert([
            ['program_id' => 'TM01', 'department_id' => 'TEKNIK', 'name' => 'Teknik Mesin', 'created_dt' => now()],
            ['program_id' => 'TO01', 'department_id' => 'TEKNIK', 'name' => 'Teknik Otomotif', 'created_dt' => now()],
            ['program_id' => 'TI01', 'department_id' => 'TI', 'name' => 'Teknik Informatika', 'created_dt' => now()],
            ['program_id' => 'MI01', 'department_id' => 'TI', 'name' => 'Manajemen Informatika', 'created_dt' => now()],
            ['program_id' => 'TE01', 'department_id' => 'TEKNIK', 'name' => 'Teknik Elektro', 'created_dt' => now()],
            ['program_id' => 'AK01', 'department_id' => 'AKUNTANSI', 'name' => 'Akuntansi', 'created_dt' => now()],
            ['program_id' => 'MN01', 'department_id' => 'ADM_NIAGA', 'name' => 'Manajemen', 'created_dt' => now()],
            ['program_id' => 'TS01', 'department_id' => 'TEKNIK', 'name' => 'Teknik Sipil', 'created_dt' => now()],
            ['program_id' => 'TK01', 'department_id' => 'TEKNIK', 'name' => 'Teknik Kimia', 'created_dt' => now()],
            ['program_id' => 'AB01', 'department_id' => 'ADM_NIAGA', 'name' => 'Administrasi Bisnis (Niaga)', 'created_dt' => now()],
        ]);
    }
}
