<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('program_id')->nullable();
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('training_center_id')->nullable();
            $table->unsignedInteger('batch_id')->nullable();

            $table->unsignedTinyInteger('payment_status')->default(0);

            $table->string('first_name', 300);
            $table->string('first_name_en', 150)->nullable();
            $table->string('last_name', 300);
            $table->string('last_name_en', 150)->nullable();
            $table->unsignedTinyInteger('gender')
                ->comment('1=>male,2=>female,3=>others');

            $table->date('date_of_birth');

            $table->string('email', 200)->nullable();
            $table->string('mobile', 20)->nullable();

            $table->unsignedTinyInteger('identity_number_type')
                ->nullable()->comment('Nid => 1, Birth Cert => 2, Passport => 3');

            $table->string('identity_number', 100)->nullable();

            $table->unsignedTinyInteger('religion')
                ->comment('1 => Islam, 2 => Hinduism, 3 => Christianity, 4 => Buddhism, 5 => Judaism, 6 => Sikhism, 7 => Ethnic, 8 => Agnostic/Atheist');

            $table->unsignedTinyInteger('marital_status')
                ->comment('1=>single,2=>married,3=>widowed,4=>divorced');

            $table->unsignedSmallInteger('nationality')->default(1);
            /** Coming from nise3 config file */

            $table->unsignedTinyInteger('physical_disability_status')
                ->comment('0=>No,1=>Yes')->default(0);

            $table->unsignedTinyInteger('freedom_fighter_status')
                ->comment('0 => No, 1 => Yes, 3=> child of a freedom fighter, 4 => grand child of a freedom fighter')
                ->default(0);

            $table->unsignedTinyInteger('does_belong_to_ethnic_group')->default(0);

            $table->string('passport_photo_path', 600)->nullable();
            $table->string('signature_image_path', 600)->nullable();

            $table->unsignedTinyInteger("row_status")->default(0)
                ->comment('0=>inactive, 1=>active, 2=>pending, 3=>rejected');

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
        Schema::dropIfExists('course_enrollments');
    }
}
