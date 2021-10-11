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
            $table->id();
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('program_id')->nullable();
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('training_center_id')->nullable();
            $table->unsignedInteger('batch_id')->nullable();
            $table->string('first_name', 300);
            $table->string('first_name_en', 150);
            $table->string('last_name', 300);
            $table->string('last_name_en', 150);
            $table->date('date_of_birth');
            $table->string('email', 191)->unique();
            $table->string('mobile', 20)->unique();

            $table->unsignedTinyInteger('identity_number_type')
                ->nullable()->comment('Nid => 1, Birth Cert => 2, Passport => 3');

            $table->string('identity_number', 100)->nullable();

            $table->unsignedTinyInteger('gender')
                ->comment('1=>male,2=>female,3=>others');

            $table->unsignedTinyInteger('religion')
                ->comment('1 => Islam, 2 => Hinduism, 3 => Christianity, 4 => Buddhism, 5 => Judaism, 6 => Sikhism, 7 => Ethnic, 8 => Agnostic/Atheist');

            $table->unsignedTinyInteger('marital_status')
                ->comment('1=>single,2=>married,3=>widowed,4=>divorced');

            $table->unsignedSmallInteger('nationality')->default(1); /** Coming from nise3 config file */

            $table->unsignedTinyInteger('physical_disability_status')
                ->comment('0=>No,1=>Yes')->default(0);


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
        Schema::dropIfExists('course_enrollments');
    }
}
