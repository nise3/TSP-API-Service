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
            $table->string('code', 150);
            $table->string('title_en', 500);
            $table->string('title_bn', 1000)->nullable();
            $table->string('domain', 191)->nullable();
            $table->text('address')->nullable();
            $table->text('google_map_src')->nullable();
            $table->text('logo')->nullable();
            $table->string('primary_phone', 15)->nullable();
            $table->string('phone_numbers', 255)->nullable();
            $table->string('primary_mobile', 15)->nullable();
            $table->string('mobile_numbers', 255)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('config')->nullable();
            $table->unsignedInteger("loc_division_id")->nullable();
            $table->unsignedInteger("loc_district_id")->nullable();
            $table->unsignedInteger("loc_upazila_id")->nullable();
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
