<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->unique();
            $table->integer('customer_id')->unsigned()->index();
            $table->integer('student_id')->unsigned()->index();
            $table->string('payment_method', 50);
            $table->integer('total');
            $table->boolean('instalment');
            $table->date('transaction_date');
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('student_id')
                ->references('id')->on('students')
                ->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
