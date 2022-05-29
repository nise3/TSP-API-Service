<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamResultPublishedAtToBatchExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_exams', function (Blueprint $table) {
            $table->dateTime('exam_result_published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_exams', function (Blueprint $table) {
            $table->dropColumn('exam_result_published_at');
        });
    }
}
