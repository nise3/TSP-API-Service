<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentProfessionalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment_professional_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_enrollment_id');
            $table->string('main_profession', 500);
            $table->string('main_profession_en', 250)->nullable();

            $table->string('other_profession', 500)->nullable();
            $table->string('other_profession_en', 250)->nullable();

            $table->double('monthly_income', 10, 2);
            $table->unsignedTinyInteger('is_currently_employed')->default(0);
            $table->unsignedTinyInteger('years_of_experiences')->default(0);
            $table->year('passing_year')->nullable();

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('course_enrollment_id', 'c_enroll_prof_fk_enrollment_id')
                ->references('id')
                ->on('course_enrollments')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enrollment_professional_infos');
    }
}
