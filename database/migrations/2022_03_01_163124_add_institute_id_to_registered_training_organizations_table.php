<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstituteIdToRegisteredTrainingOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registered_training_organizations', function (Blueprint $table) {
            $table->unsignedInteger('institute_id')->after('id')->index('rto_fk_institute_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registered_training_organizations', function (Blueprint $table) {
            $table->dropColumn('institute_id');
        });
    }
}
