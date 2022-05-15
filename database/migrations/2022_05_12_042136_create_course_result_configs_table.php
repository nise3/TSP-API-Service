<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseResultConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_result_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('institute_id')->nullable();
            $table->unsignedInteger('industry_association_id')->nullable();
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('result_type')->comment("1=>GRADING, 2=>MARKS");
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
        Schema::dropIfExists('course_result_configs');
    }
}
