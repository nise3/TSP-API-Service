<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarksUpdatedAtToYouthExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youth_exams', function (Blueprint $table) {
            $table->dateTime('marks_updated_at')->after('total_obtained_marks')->nullable();
            $table->unsignedInteger('exam_type_id')->nullable()->change();        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('youth_exams', function (Blueprint $table) {
            $table->dropColumn('marks_updated_at');
            $table->unsignedInteger('exam_type_id')->nullable(false);
        });
    }
}
