<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_enrollment_id');

            $table->unsignedTinyInteger('address_type')
                ->comment('1 => Present, 2 => Permanent')
                ->default(1);

            $table->unsignedMediumInteger("loc_division_id");
            $table->unsignedMediumInteger("loc_district_id");
            $table->unsignedMediumInteger("loc_upazila_id")->nullable();

            $table->string("village_n_area", 500)->nullable();
            $table->string("village_or_area_en", 250)->nullable();

            $table->string("house_n_road", 500)->nullable();
            $table->string("house_n_road_en", 250)->nullable();

            $table->string("zip_or_postal_code", 10)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_enrollment_id', 'c_enroll_address_fk_enrollment_id')
                ->references('id')
                ->on('course_enrollments')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('loc_division_id')
                ->references('id')
                ->on('loc_divisions')
                ->onDelete("CASCADE")
                ->onUpdate("CASCADE");

            $table->foreign('loc_district_id')
                ->references('id')
                ->on('loc_districts')
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
        Schema::dropIfExists('enrollment_addresses');
    }
}
