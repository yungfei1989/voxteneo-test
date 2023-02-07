<?php

use Illuminate\Database\Seeder;

class TransactionLinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_lines')->insert([
            'id' => 1,
            'transaction_code' => 'TRX0001',
            'item_code' => 'SCH',
            'price' => 2000000,
            'description' => 'Donation for 1 year',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('transaction_lines')->insert([
            'id' => 2,
            'transaction_code' => 'TRX0001',
            'item_code' => 'BOOK',
            'price' => 5000000,
            'description' => 'Donation for 1 year',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
