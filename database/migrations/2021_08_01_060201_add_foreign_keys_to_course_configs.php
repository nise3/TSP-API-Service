<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCourseConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_configs', function (Blueprint $table) {
            $table->foreign('branch_id', 'course_configs_fk_branch_id')
                ->references('id')->on('branches')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');

            $table->foreign('course_id', 'course_configs_fk_course_id')
                ->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('RESTRICT');

            $table->foreign('institute_id', 'course_configs_fk_institute_id')
                ->references('id')->on('institutes')->onUpdate('CASCADE')->onDelete('RESTRICT');

            $table->foreign('programme_id', 'course_configs_fk_programme_id')
                ->references('id')->on('programmes')->onUpdate('CASCADE')->onDelete('SET NULL');

            $table->foreign('training_center_id', 'course_configs_fk_training_center_id')
                ->references('id')->on('training_centers')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_configs', function (Blueprint $table) {
            $table->dropForeign('course_configs_fk_branch_id');
            $table->dropForeign('course_configs_fk_course_id');
            $table->dropForeign('course_configs_fk_institute_id');
            $table->dropForeign('course_configs_fk_programme_id');
            $table->dropForeign('course_configs_fk_training_center_id');
        });
    }
}
