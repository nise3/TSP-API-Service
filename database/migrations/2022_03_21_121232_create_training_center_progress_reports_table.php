<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingCenterProgressReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_center_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('training_center_id');
            $table->dateTime('reporting_month');
            $table->string('trade_name', 200)->nullable();
            $table->unsignedInteger('number_of_trainers')->default(0);
            $table->unsignedInteger('number_of_labs_or_training_rooms')->default(0);
            $table->unsignedInteger('number_of_computers_or_training_equipments')->default(0);

            $table->unsignedInteger('admitted_trainee_men')->default(0);
            $table->unsignedInteger('admitted_trainee_women')->default(0);
            $table->unsignedInteger('admitted_trainee_disabled')->default(0);
            $table->unsignedInteger('admitted_trainee_qawmi')->default(0);
            $table->unsignedInteger('admitted_trainee_transgender')->default(0);
            $table->unsignedInteger('admitted_trainee_others')->default(0);
            $table->unsignedInteger('admitted_trainee_total')->default(0);

            $table->unsignedInteger('technical_board_registered_trainee_men')->default(0);
            $table->unsignedInteger('technical_board_registered_trainee_women')->default(0);
            $table->unsignedInteger('technical_board_registered_trainee_disabled')->default(0);
            $table->unsignedInteger('technical_board_registered_trainee_qawmi')->default(0);
            $table->unsignedInteger('technical_board_registered_trainee_transgender')->default(0);
            $table->unsignedInteger('technical_board_registered_trainee_others')->default(0);
            $table->unsignedInteger('technical_board_registered_trainee_total')->default(0);

            $table->unsignedInteger('latest_test_attended_trainee_men')->default(0);
            $table->unsignedInteger('latest_test_attended_trainee_women')->default(0);
            $table->unsignedInteger('latest_test_attended_trainee_disabled')->default(0);
            $table->unsignedInteger('latest_test_attended_trainee_qawmi')->default(0);
            $table->unsignedInteger('latest_test_attended_trainee_transgender')->default(0);
            $table->unsignedInteger('latest_test_attended_trainee_others')->default(0);
            $table->unsignedInteger('latest_test_attended_trainee_total')->default(0);

            $table->unsignedInteger('latest_test_passed_trainee_men')->default(0);
            $table->unsignedInteger('latest_test_passed_trainee_women')->default(0);
            $table->unsignedInteger('latest_test_passed_trainee_disabled')->default(0);
            $table->unsignedInteger('latest_test_passed_trainee_qawmi')->default(0);
            $table->unsignedInteger('latest_test_passed_trainee_transgender')->default(0);
            $table->unsignedInteger('latest_test_passed_trainee_others')->default(0);
            $table->unsignedInteger('latest_test_passed_trainee_total')->default(0);

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
        Schema::dropIfExists('training_center_progress_reports');
    }
}
