<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCourseSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->foreign('course_id', 'course_sessions_fk_course_id')->references('id')->on('course_configs')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('course_config_id', 'course_sessions_fk_course_config_id')->references('id')->on('course_configs')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->dropForeign('course_sessions_fk_course_id');
            $table->dropForeign('course_sessions_fk_course_config_id');
        });
    }
}
