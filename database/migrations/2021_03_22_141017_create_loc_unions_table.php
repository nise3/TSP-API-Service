<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocUnionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('loc_unions', static function (Blueprint $table) {

            $table->increments('id');
            $table->string('title');
            $table->string('title_en')->nullable();

            $table->unsignedMediumInteger('loc_division_id');
            $table->unsignedMediumInteger('loc_district_id');
            $table->unsignedInteger('loc_upazila_id');

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

        Schema::dropIfExists('loc_unions');

    }

}
