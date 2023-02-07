<?php

use Illuminate\Database\Seeder;

class StudentSchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_schools')->insert([
            'id' => 1,
            'school_id' => 1,
            'student_id' => 1,
            'period' => '2018',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
