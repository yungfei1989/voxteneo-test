<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_schools', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned()->index();
            $table->integer('school_id')->unsigned()->index();
            $table->string('period');
            $table->timestamps();

            $table->foreign('school_id')
                ->references('id')->on('schools')
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
        Schema::dropIfExists('student_schools');
    }
}
