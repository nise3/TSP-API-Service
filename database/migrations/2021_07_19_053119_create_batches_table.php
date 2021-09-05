<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('programme_id')->nullable();
            $table->unsignedInteger('institute_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('training_center_id');
            $table->unsignedSmallInteger('number_of_seats');
            $table->dateTime('registration_start_date');
            $table->dateTime('registration_end_date');
            $table->dateTime('batch_start_date');
            $table->dateTime('batch_end_date');
            $table->unsignedSmallInteger('available_seats')->default(0);
            $table->text('dynamic_form_field')->nullable();
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
        Schema::dropIfExists('batches');
    }
}
