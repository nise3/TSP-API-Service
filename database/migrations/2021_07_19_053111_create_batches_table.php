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
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('institute_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('training_center_id');
            $table->unsignedSmallInteger('number_of_seats');
            $table->date('registration_start_date')->comment('Date format = Y-m-d');
            $table->date('registration_end_date')->comment('Date format = Y-m-d');
            $table->date('batch_start_date')->comment('Date format = Y-m-d');
            $table->date('batch_end_date')->comment('Date format = Y-m-d');
            $table->unsignedSmallInteger('available_seats')->default(0);
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
