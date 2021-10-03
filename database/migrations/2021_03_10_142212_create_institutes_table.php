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
            $table->unsignedInteger('institute_type_id');
            $table->string('code', 150);
            $table->string('title_en', 500);
            $table->string('title_bn', 1000); // TODO: _bn should be removed
            $table->string('domain', 191)->nullable();
            $table->unsignedInteger("loc_division_id")->nullable(); // TODO: it should be unsigned medium integer
            $table->unsignedInteger("loc_district_id")->nullable(); // TODO: it should be unsigned medium integer
            $table->unsignedInteger("loc_upazila_id")->nullable(); // TODO: it should be unsigned medium integer
            $table->text('address')->nullable(); // TODO: need separate column for english
            $table->text('google_map_src')->nullable();
            $table->text('logo')->nullable(); // TODO: column type should be string (600)
            $table->string("country")->default("BD")->comment('ISO country code');
            $table->string("phone_code")->default("880")->comment('Country phone code');
            $table->string('primary_phone', 15)->nullable();
            $table->string('phone_numbers', 255)->nullable(); // TODO: size should be 20
            $table->string('primary_mobile', 15);
            $table->string('mobile_numbers', 255)->nullable(); // TODO: size should be 15
            $table->string('email', 191);
            $table->string("name_of_the_office_head")->nullable(); // TODO: need separate column for english
            $table->string("name_of_the_office_head_designation")->nullable(); // TODO: need separate column for english
            $table->string('contact_person_name', 500); // TODO: need separate column for english
            $table->string('contact_person_mobile', 15);
            $table->string('contact_person_email', 191);
            $table->string('contact_person_designation', 300); // TODO: need separate column for english
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
