<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('photo')->nullable();
            $table->string('gender');
            $table->date('birth_date');
            $table->integer('nisn')->unique();
            $table->string('slh_location');
            $table->string('academy');
            $table->string('grade');
            $table->string('parent_b');
            $table->string('dream');
            $table->string('school_code')->nullable();
            $table->integer('is_sponsored')->default(0);
            $table->text('parent_background_en');
            $table->text('parent_background_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
