<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('batch_id')->nullable();
            $table->unsignedInteger('exam_id')->nullable();
            $table->unsignedInteger('youth_id')->nullable();
            $table->unsignedTinyInteger('exam_type')->comment('1=> Online, 2=> Offline, 3=> Mixed, 4=>Practical, 5=> Field Work, 6=> Presentation, 7=> Assignment');
            $table->unsignedInteger('result_type')->comment("1=> COMPETENT, 2=> NOT COMPETENT, 3=>GRADING, 4=>MARKS, 5=> PARTICIPATION");
            $table->json('gradings')->nullable();
            $table->unsignedTinyInteger('is_attendance_marks_count')->default(0)->comment("0 => FALSE, 1 => TRUE");
            $table->softDeletes();
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
        Schema::dropIfExists('exam_marks');
    }
}
