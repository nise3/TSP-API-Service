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
            $table->unsignedSmallInteger('number_of_vacancies');
            $table->dateTime('registration_start_date');
            $table->dateTime('registration_end_date');
            $table->dateTime('batch_start_date');
            $table->dateTime('batch_end_date');
            $table->unsignedSmallInteger('available_vacancies')->default(0);
            $table->boolean('in_ethnic_group')->nullable()->default(false)->comment("1 => in ethnic group, 2 => not in ethnic group");
            $table->boolean('is_freedom_fighter')->nullable()->default(false);
            $table->boolean('disability_status')->nullable()->default(false)->comment('1 => disable  2 => not disable');
            $table->boolean('ssc_passing_status')->nullable()->default(false)->comment('is passed ssc');
            $table->boolean('hsc_passing_status')->nullable()->default(false)->comment('is passed hsc');
            $table->boolean('honors_passing_status')->nullable()->default(false)->comment('is passed honors');
            $table->boolean('masters_passing_status')->nullable()->default(false)->comment('is passed masters');
            $table->boolean('is_occupation_needed')->nullable()->default(false)->comment('is occupation needed');
            $table->boolean('is_guardian_info_needed')->nullable()->default(false)->comment('is guardian information needed');
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
