<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseIdToCertificateIssued extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificate_issued', function (Blueprint $table) {
            $table->unsignedTinyInteger('course_id')
                ->nullable()
                ->after('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificate_issued', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
    }
}
