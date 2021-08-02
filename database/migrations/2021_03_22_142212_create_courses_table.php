<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_en', 191);
            $table->string('title_bn', 1000);
            $table->unsignedDecimal('course_fee', 12)->default(0);
            $table->unsignedDecimal('duration', 14)->nullable()->comment('Duration in hours');
            $table->text('description')->nullable();
            $table->string('target_group', 500)->nullable();
            $table->text('objects')->nullable();
            $table->text('contents')->nullable();
            $table->string('training_methodology', 300)->nullable();
            $table->string('evaluation_system', 300)->nullable();
            $table->text('prerequisite')->nullable();
            $table->text('eligibility')->nullable();
            $table->string('cover_image', 191)->nullable();
            $table->string('code', 191);
            $table->unsignedInteger('institute_id')->index('courses_fk_institute_id');
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('courses');
    }
}
