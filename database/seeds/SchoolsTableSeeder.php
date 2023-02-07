<?php

use Illuminate\Database\Seeder;

class SchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schools')->insert([
            'id' => 1,
            'name' => 'London School',
            'code' => 'LSCH',
            'phone' => '+628567787878',
            'region' => 'Banten',
            'address' => 'Tangerang',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
