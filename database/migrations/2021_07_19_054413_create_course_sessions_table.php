<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('course_id');
            $table->unsignedInteger('course_config_id');

            $table->string('session_name_en');
            $table->string('session_name_bn', 300);

            $table->unsignedTinyInteger('number_of_batches');
            $table->dateTime('application_start_date');
            $table->dateTime('application_end_date');
            $table->dateTime('course_start_date');
            $table->unsignedSmallInteger('max_seat_available')->default(0);
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
        Schema::dropIfExists('course_sessions');
    }
}
