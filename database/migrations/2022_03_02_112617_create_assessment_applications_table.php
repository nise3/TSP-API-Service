<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rpl_sector_id')->index();
            $table->unsignedInteger('rpl_occupation_id')->index();
            $table->unsignedInteger('rpl_level_id')->index();
            $table->unsignedInteger('youth_id')->index();
            $table->unsignedInteger('assessment_id')->index();
            $table->unsignedInteger('target_country_id')->index();
            $table->unsignedInteger('rto_country_id')->index();
            $table->unsignedInteger('rto_id')->index();
            $table->unsignedInteger('rto_batch_id')->index()->nullable();
            $table->unsignedTinyInteger('result')->comment('0=>FAIL,1=>PASS')->nullable();
            $table->unsignedFloat('score')->comment('percentage score')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youth_assessments');
    }
}
