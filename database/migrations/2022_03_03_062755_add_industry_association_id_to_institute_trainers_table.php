<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndustryAssociationIdToInstituteTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institute_trainers', function (Blueprint $table) {
            $table->unsignedInteger('industry_association_id')->nullable();
            $table->unsignedInteger('institute_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *n
     * @return void
     */
    public function down()
    {
        Schema::table('institute_trainers', function (Blueprint $table) {
            $table->dropColumn('industry_association_id');
            $table->unsignedInteger('institute_id')->nullable(false)->change();
        });
    }
}
