<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRtoIdAssessmentDateToRtoBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rto_batches', function (Blueprint $table) {
            $table->integer('rto_id')->after('assessor_id');
            $table->dateTime('assessment_date')->nullable()->after('rto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rto_batches', function (Blueprint $table) {
            $table->dropColumn('rto_id');
            $table->dropColumn('assessment_date');
        });
    }
}
