<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveInstituteIdToTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn('institute_id');
            $table->dropColumn('trainer_id');
            $table->unsignedInteger('youth_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('trainer_id');
            $table->dropColumn('youth_id');
        });
    }
}
