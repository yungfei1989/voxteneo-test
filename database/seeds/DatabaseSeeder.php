<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(SchoolsTableSeeder::class);
        $this->call(StudentsTableSeeder::class);
        $this->call(StudentSchoolsTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(PriceListsTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(TransactionLinesTableSeeder::class);
    }
}
