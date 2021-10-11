<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentMiscellaneousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment_miscellaneouses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_enrollment_id');
            $table->unsignedTinyInteger('has_own_family_home');
            $table->unsignedTinyInteger('has_own_family_land');
            $table->unsignedTinyInteger('number_of_siblings');
            $table->unsignedTinyInteger('recommended_by_any_organization')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_enrollment_id', 'c_enroll_mesc_fk_enrollment_id')
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
        Schema::dropIfExists('enrollment_miscellaneouses');
    }
}
