<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_code', 100)->index();
            $table->string('item_code', 100)->index();
            $table->integer('price');
            $table->text('description');
            $table->timestamps();

            $table->foreign('transaction_code')
                ->references('code')->on('transactions')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('item_code')
                ->references('code')->on('items')
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
        Schema::dropIfExists('transaction_lines');
    }
}
