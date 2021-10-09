<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('institute_id');
            $table->string('title', 600);
            $table->string('title_en', 250)->nullable();
            $table->unsignedMediumInteger("loc_division_id")->nullable();
            $table->unsignedMediumInteger("loc_district_id")->nullable();
            $table->unsignedMediumInteger("loc_upazila_id")->nullable();
            $table->text('address')->nullable();
            $table->text('address_en')->nullable();
            $table->string('location_latitude', 50)->nullable();
            $table->string('location_longitude', 50)->nullable();
            $table->text('google_map_src')->nullable();
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
        Schema::dropIfExists('branches');
    }
}
