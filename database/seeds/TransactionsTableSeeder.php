<?php

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Transaction;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            'id' => 1,
            'customer_id' => 1,
            'student_id' => 1,
            'code' => 'TRX0001',
            'transaction_date' => date('Y-m-d'),
            'total' => 7000000,
            'payment_method' => 'TRANSFER',
            'instalment' => false,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
