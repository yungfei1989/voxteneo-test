<?php

use Illuminate\Database\Seeder;

class PriceListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('price_lists')->insert([
            'id' => 1,
            'school_code' => 'LSCH',
            'item_code' => 'BOOK',
            'start_year' => '2017',
            'end_year' => '2018',
            'price' => 2000000,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('price_lists')->insert([
            'id' => 2,
            'school_code' => 'LSCH',
            'item_code' => 'SCH',
            'start_year' => '2017',
            'end_year' => '2018',
            'price' => 5000000,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
