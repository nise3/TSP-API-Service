<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentGuardiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment_guardians', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('course_enrollment_id');
            $table->string('father_name', 500);
            $table->string('father_name_en', 250)->nullable();
            $table->string('father_nid', 30)->nullable();
            $table->string('father_mobile', 20)->nullable();
            $table->date('father_date_of_birth')->nullable();

            $table->string('mother_name', 500);
            $table->string('mother_name_en', 250)->nullable();
            $table->string('mother_nid', 30)->nullable();
            $table->string('mother_mobile', 20)->nullable();
            $table->date('mother_date_of_birth')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_enrollment_id', 'c_enroll_guard_fk_enrollment_id')
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
        Schema::dropIfExists('enrollment_guardians');
    }
}
