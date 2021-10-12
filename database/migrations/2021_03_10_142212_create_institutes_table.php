<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('institute_type_id')->default(0);
            $table->string('code', 150);
            $table->string('title', 1000);
            $table->string('title_en', 500)->nullable();
            $table->string('domain', 191)->nullable();

            $table->unsignedMediumInteger('loc_division_id')->nullable()->index('tsp_ins_loc_division_id_inx');
            $table->unsignedMediumInteger('loc_district_id')->nullable()->index('tsp_ins_loc_district_id_inx');
            $table->unsignedMediumInteger('loc_upazila_id')->nullable()->index('tsp_ins_loc_upazila_id_inx');
            $table->string('location_latitude', 50)->nullable();
            $table->string('location_longitude', 50)->nullable();
            $table->text('google_map_src')->nullable();

            $table->text('address_en')->nullable();
            $table->text('address')->nullable();

            $table->string('logo', 600)->nullable();
            $table->string("country")->default("BD")->comment('ISO country code');
            $table->string("phone_code")->default("880")->comment('Country phone code');
            $table->string('primary_phone', 20)->nullable();
            $table->string('phone_numbers', 400)->nullable();
            $table->string('primary_mobile', 15);
            $table->string('mobile_numbers', 400)->nullable();
            $table->string('email', 150);
            $table->string("name_of_the_office_head", 500)->nullable();
            $table->string("name_of_the_office_head_en")->nullable();
            $table->string("name_of_the_office_head_designation", 500)->nullable();
            $table->string("name_of_the_office_head_designation_en")->nullable();
            $table->string('contact_person_name', 500);
            $table->string('contact_person_name_en', 250)->nullable();
            $table->string('contact_person_mobile', 15);
            $table->string('contact_person_email', 191);
            $table->string('contact_person_designation', 500);
            $table->string('contact_person_designation_en', 300)->nullable();
            $table->text('config')->nullable();

            $table->unsignedTinyInteger('row_status')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institutes');
    }
}
