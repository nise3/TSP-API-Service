<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBatchExamsTable extends Migration
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
            $table->dateTime('mark_updated_at')->nullable();
            $table->unsignedInteger('exam_type_id')->nullable()->change();
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
            $table->dropColumn('result_published_at');
            $table->dropColumn('mark_updated_at');
            $table->unsignedInteger('exam_type_id')->nullable(false);
        });
    }
}
