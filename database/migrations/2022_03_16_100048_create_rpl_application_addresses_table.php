<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRplApplicationAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rpl_application_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rpl_application_id');

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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rpl_application_addresses');
    }
}
