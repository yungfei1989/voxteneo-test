<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_code', 100)->index();
            $table->string('item_code', 100)->index();
            $table->string('start_year', 10);
            $table->string('end_year', 10);
            $table->string('price', 20);
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_code')
                ->references('code')->on('schools')
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
        Schema::dropIfExists('price_lists');
    }
}
