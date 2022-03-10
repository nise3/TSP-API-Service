<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYouthDetailsToYouthAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youth_assessments', function (Blueprint $table) {
            $table->json('youth_details')->after('youth_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('youth_assessments', function (Blueprint $table) {
            $table->dropColumn('youth_details');
        });
    }
}
