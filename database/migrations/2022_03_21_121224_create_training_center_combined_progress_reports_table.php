<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingCenterCombinedProgressReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_center_combined_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('training_center_id');
            $table->dateTime('reporting_month');

            $table->unsignedInteger('voluntary_organizations_registered_in_current_month')->default(0);
            $table->unsignedInteger('members_up_to_previous_month_general_members')->default(0);
            $table->unsignedInteger('members_up_to_previous_month_life_member')->default(0);
            $table->unsignedInteger('members_up_to_previous_month_patron_member')->default(0);
            $table->unsignedInteger('members_up_to_previous_month_total')->default(0);
            $table->unsignedInteger('member_enrollment_in_reporting_month_general_members')->default(0);
            $table->unsignedInteger('member_enrollment_in_reporting_month_life_member')->default(0);
            $table->unsignedInteger('member_enrollment_in_reporting_month_patron_member')->default(0);
            $table->unsignedInteger('member_enrollment_in_reporting_month_total')->default(0);
            $table->unsignedInteger('total_number_of_members')->default(0);
            $table->unsignedDecimal('subscriptions_collected_so_far')->default(0);
            $table->unsignedDecimal('subscriptions_collected_in_current_month_organization')->default(0);
            $table->unsignedDecimal('subscriptions_collected_in_current_month_member')->default(0);
            $table->unsignedDecimal('subscriptions_collected_in_current_month_total')->default(0);
            $table->text('grants_received_in_current_month_source')->nullable();
            $table->unsignedDecimal('grants_received_in_current_month_amount')->default(0);
            $table->unsignedDecimal('grants_received_in_current_month_total')->default(0);
            $table->unsignedDecimal('gross_income')->default(0);

            $table->unsignedDecimal('income_in_skills_development_sector_trades')->default(0);
            $table->unsignedDecimal('income_in_skills_development_sector_money')->default(0);
            $table->unsignedDecimal('expenditure_in_skill_development_training')->default(0);
            $table->unsignedDecimal('expenditure_in_other_sectors')->default(0);
            $table->unsignedDecimal('expenditure_total')->default(0);
            $table->unsignedDecimal('total_income_in_the_training_sector')->default(0);
            $table->text('bank_status_and_account_number')->nullable();
            $table->unsignedDecimal('bank_interest')->default(0);
            $table->text('amount_of_fdr_and_bank_account_number')->nullable();
            $table->unsignedInteger('number_of_meetings_held_during_current_financial_year')->default(0);
            $table->unsignedInteger('number_of_executive_council_meetings_in_current_month')->default(0);
            $table->text('names_and_numbers_of_other_meetings')->nullable();
            $table->unsignedInteger('coordinating_council_meeting_total')->default(0);
            $table->text('other_activities_undertaken')->nullable();


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
        Schema::dropIfExists('training_center_combined_progress_reports');
    }
}
