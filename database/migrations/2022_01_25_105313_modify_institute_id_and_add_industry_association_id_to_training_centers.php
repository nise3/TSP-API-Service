<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyInstituteIdAndAddIndustryAssociationIdToTrainingCenters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_centers', function (Blueprint $table) {
            DB::statement('ALTER TABLE training_centers MODIFY institute_id int(255) NUll');
            $table->unsignedInteger('industry_association_id')->nullable()->after('institute_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_centers', function (Blueprint $table) {
            DB::statement('ALTER TABLE training_centers MODIFY institute_id int(255) NOT NUll');
            $table->dropColumn('industry_association_id');
        });
    }
}
