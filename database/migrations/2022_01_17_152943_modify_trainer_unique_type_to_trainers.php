<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyTrainerUniqueTypeToTrainers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainers', function (Blueprint $table) {
            DB::statement('ALTER TABLE trainers DROP INDEX trainers_email_unique');
            DB::statement('ALTER TABLE trainers DROP INDEX trainers_trainer_registration_number_unique');
            DB::statement('ALTER TABLE trainers DROP INDEX trainers_mobile_unique');
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
          $table->unique('email', 'trainers_email_unique');
          $table->unique('trainer_registration_number', 'trainers_trainer_registration_number_unique');
          $table->unique('mobile', 'trainers_mobile_unique');
        });
    }
}
