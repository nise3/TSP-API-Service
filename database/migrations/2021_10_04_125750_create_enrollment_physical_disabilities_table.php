<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentPhysicalDisabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment_physical_disabilities', function (Blueprint $table) {
            $table->unsignedInteger('course_enrollment_id');
            $table->unsignedTinyInteger('physical_disability_id');

            $table->foreign('course_enrollment_id', 'c_enroll_physical_disability_fk_enrollment_id')
                ->references('id')
                ->on('course_enrollments')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('physical_disability_id')
                ->references('id')
                ->on('physical_disabilities')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youth_physical_disabilities');
    }
}
