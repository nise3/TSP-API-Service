<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocCityCorporationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('loc_city_corporations', static function (Blueprint $table) {

            $table->mediumIncrements('id');

            $table->string('title', 355);
            $table->string('title_en')->nullable();

            $table->mediumInteger('loc_division_id')->unsigned()->default(0);
            $table->mediumInteger('loc_district_id')->unsigned()->default(0);

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

        Schema::drop('loc_city_corporations');

    }

}
