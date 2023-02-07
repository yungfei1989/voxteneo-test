<?php

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            'id' => 1,
            'name' => 'Book',
            'code' => 'BOOK',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('items')->insert([
            'id' => 2,
            'name' => 'Beasiswa',
            'code' => 'SCH',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
