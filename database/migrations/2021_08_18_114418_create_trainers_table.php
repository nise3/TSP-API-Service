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

            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('training_center_id')->nullable();

            $table->string('trainer_name', 500);
            $table->string('trainer_name_en', 250);
            $table->string('trainer_registration_number', 100)->unique();
            $table->string('email', 150)->unique();
            $table->string('mobile', 15)->unique();
            $table->date("date_of_birth")->nullable();
            $table->text('about_me')->nullable();
            $table->text('about_me_en')->nullable();
            $table->text('educational_qualification')->nullable();
            $table->text('educational_qualification_en')->nullable();
            $table->text('skills')->nullable();
            $table->text('skills_en')->nullable();

            $table->unsignedTinyInteger('gender')->comment('1=>male,2=>female,3=>others')->default(1);
            $table->unsignedTinyInteger('marital_status')->comment('0=>No,1=>Yes')->default(0);
            $table->unsignedTinyInteger('religion')->comment('1=>islam,2=>hindu,3=>buddhist,4=>Christians,5=>others')->nullable();
            $table->string('nationality', 100)->default('Bangladeshi');
            $table->string('nid', 30)->nullable();
            $table->string('passport_number', 50)->nullable();

            $table->unsignedMediumInteger('present_address_division_id')->nullable();
            $table->unsignedMediumInteger('present_address_district_id')->nullable();
            $table->unsignedMediumInteger('present_address_upazila_id')->nullable();

            $table->text('present_house_address')->nullable();
            $table->text('present_house_address_en')->nullable();

            $table->unsignedMediumInteger('permanent_address_division_id')->nullable();
            $table->unsignedMediumInteger('permanent_address_district_id')->nullable();
            $table->unsignedMediumInteger('permanent_address_upazila_id')->nullable();

            $table->text('permanent_house_address')->nullable();
            $table->text('permanent_house_address_en')->nullable();

            $table->text('photo')->nullable();
            $table->text('signature')->nullable();

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
        Schema::dropIfExists('trainers');
    }
}
