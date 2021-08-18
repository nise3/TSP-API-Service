<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('institute_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('training_center_id')->nullable();
            $table->string('trainer_name_en', 191);
            $table->string('trainer_name_bn', 500);
            $table->string('trainer_registration_number', 191)->unique();
            $table->string('email', 191)->unique();
            $table->string('mobile', 191)->unique();
            $table->date("date_of_birth")->nullable();
            $table->string('about_me', 255)->nullable();
            $table->tinyInteger('gender')->comment('1=>male,2=>female,3=>others')->default(1);
            $table->boolean('marital_status')->comment('0=>No,1=>Yes')->default(0);
            $table->tinyInteger('religion')->comment('1=>islam,2=>hindu,3=>buddhist,4=>Christians,5=>others')->nullable();
            $table->string('nationality', 191);
            $table->string('nid', 50)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->tinyInteger('physical_disabilities_status')->comment('0=>No,1=>Yes')->default(0);
            $table->tinyInteger('freedom_fighter_status')->comment('1=>Yes,0=>No')->default(0);
            $table->tinyInteger('present_address_division_id')->nullable();
            $table->tinyInteger('present_address_district_id')->nullable();
            $table->smallInteger('present_address_upazila_id')->nullable();
            $table->string('present_house_address', 255)->nullable();
            $table->tinyInteger('permanent_address_division_id')->nullable();
            $table->tinyInteger('permanent_address_district_id')->nullable();
            $table->smallInteger('permanent_address_upazila_id')->nullable();
            $table->string('permanent_house_address', 255)->nullable();
            $table->string('educational_qualification', 191)->nullable();
            $table->string('skills', 191)->nullable();
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->boolean('row_status')->default(1);
            $table->unsignedSmallInteger('created_by')->nullable();
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainers');
    }
}
