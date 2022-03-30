<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */


    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('exam_type_id');
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('venue')->nullable();
            $table->unsignedDecimal('total_marks')->default(0);
            $table->unsignedTinyInteger('row_status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
