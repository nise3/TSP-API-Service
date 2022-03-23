<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class
CreateTrainingCenterIncomeExpenditureReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_center_income_expenditure_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('training_center_id');
            $table->dateTime('reporting_month');

            $table->string('trade_name', 200)->nullable();
            $table->unsignedInteger('number_of_labs_or_training_rooms')->default(0);
            $table->unsignedInteger('number_of_allowed_seats')->default(0);
            $table->unsignedInteger('number_of_trainees')->default(0);
            $table->unsignedDecimal('course_fee_per_trainee')->default(0);

            $table->unsignedDecimal('course_income_from_course_fee')->default(0);
            $table->unsignedDecimal('course_income_from_application_and_others')->default(0);
            $table->unsignedDecimal('course_income_total')->default(0);

            $table->unsignedDecimal('reporting_month_income')->default(0);

            $table->unsignedDecimal('reporting_month_training_expenses_instructor_salaries')->default(0);
            $table->unsignedDecimal('reporting_month_training_expenses_other')->default(0);
            $table->unsignedDecimal('reporting_month_training_expenses_total')->default(0);

            $table->decimal('reporting_month_net_income')->default(0);

            $table->text('bank_status_up_to_previous_month')->nullable();
            $table->text('bank_status_so_far')->nullable();

            $table->text('account_no_and_bank_branch_name')->nullable();
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
        Schema::dropIfExists('training_center_income_expenditure_reports');
    }
}
