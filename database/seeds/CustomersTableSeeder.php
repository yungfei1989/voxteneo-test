<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            'id' => 1,
            'name' => 'Dwikky Maradhiza',
            'email' => 'dwikkymaradhiza@gmail.com',
            'password' => bcrypt('admin123'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
