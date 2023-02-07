<?php

use Illuminate\Database\Seeder;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('students')->insert([
            'id' => 1,
            'nisn' => 212,
            'first_name' => 'Dwikky',
            'last_name' => 'Yudakusuma',
            'school_code' => 'LSCH',
            'gender' => 'M',
            'slh_location' => 'Tangerang',
            'academy' => 'Testing',
            'grade' => 'A',
            'parent_b' => 'Zainal',
            'dream' => 'CEO',
            'parent_background_en' => 'Testing',
            'birth_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
