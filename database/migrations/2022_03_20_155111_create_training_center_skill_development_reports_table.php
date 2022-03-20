<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingCenterSkillDevelopmentReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_center_skill_development_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('training_center_id');
            $table->dateTime('reporting_month');
            $table->unsignedInteger('number_of_trades_allowed')->nullable();
            $table->unsignedInteger('number_of_ongoing_trades')->nullable();
            $table->unsignedInteger('number_of_computer')->nullable();
            $table->unsignedInteger('number_of_other_equipment')->nullable();
            $table->unsignedInteger('amount_of_total_fdr')->nullable();
            $table->unsignedInteger('current_session_trainees_women')->nullable();
            $table->unsignedInteger('current_session_trainees_men')->nullable();
            $table->unsignedInteger('current_session_trainees_disabled_and_others')->nullable();
            $table->unsignedInteger('current_session_trainees_total')->nullable();
            $table->unsignedInteger('total_trainees_women')->nullable();
            $table->unsignedInteger('total_trainees_men')->nullable();
            $table->unsignedInteger('total_trainees_disabled_and_others')->nullable();
            $table->unsignedInteger('total_trainees_total')->nullable();
            $table->unsignedInteger('bank_status_skill_development')->nullable();
            $table->unsignedInteger('bank_status_coordinating_council')->nullable();
            $table->dateTime('date_of_last_election_of_all_party_council')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('training_center_skill_development_reports');
    }
}
