<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('batch_id')->nullable();
            $table->unsignedTinyInteger('exam_type')->comment('1=>online, 2=>offline, 3=>mixed, 4=>Practical, 5=>Field Work, 6=>Presentation, 7=>Assignment, 8=>Attendance');
            $table->unsignedDecimal('total_marks')->nullable();
            $table->unsignedDecimal('obtained_marks')->nullable();
            $table->unsignedDecimal('percentage')->nullable();
            $table->unsignedDecimal('final_marks')->nullable();
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
        Schema::dropIfExists('result_summaries');
    }
}
