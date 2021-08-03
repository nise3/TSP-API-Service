<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('institute_id')->index('publish_courses_fk_institute_id');
            $table->unsignedInteger('branch_id')->nullable()->index('publish_courses_fk_branch_id');
            $table->unsignedInteger('training_center_id')->nullable()->index('publish_courses_fk_training_center_id');
            $table->unsignedInteger('programme_id')->nullable()->index('publish_courses_fk_programme_id');
            $table->unsignedInteger('course_id')->index('publish_courses_fk_course_id');
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->boolean('ethnic')->nullable()->default(false)->comment("1 => in ethnic group, 2 => not in ethnic group");
            $table->boolean('freedom_fighter')->nullable()->default(false);
            $table->boolean('disable_status')->nullable()->default(false)->comment('1 => disable  2 => not disable');
            $table->boolean('ssc')->nullable()->default(false)->comment('is passed ssc');
            $table->boolean('hsc')->nullable()->default(false)->comment('is passed hsc');
            $table->boolean('honors')->nullable()->default(false)->comment('is passed honors');
            $table->boolean('masters')->nullable()->default(false)->comment('is passed masters');
            $table->boolean('occupation')->nullable()->default(false)->comment('is occupation needed');
            $table->boolean('guardian')->nullable()->default(false)->comment('is guardian information needed');            $table->unsignedInteger('created_by')->nullable();
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
        Schema::dropIfExists('course_configs');
    }
}
