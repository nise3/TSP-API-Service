<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocUnionsMunicipalityCityAreasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('loc_unions_municipality_city_areas', static function (Blueprint $table) {

            $table->increments('id');
            $table->string('title');
            $table->string('title_en')->nullable();

            $table->unsignedTinyInteger('type_of')->default(1)
                ->comment('1 => Union, 2 => Municipality, 3 => City Area');

            $table->unsignedMediumInteger('loc_division_id');
            $table->unsignedMediumInteger('loc_district_id');

            $table->unsignedMediumInteger('loc_city_corporation_id')->nullable();
            $table->unsignedInteger('loc_upazila_id')->nullable();

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

        Schema::dropIfExists('loc_unions_municipality_city_areas');

    }

}
