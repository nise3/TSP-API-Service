<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_en', 191)->nullable();
            $table->string('title_bn', 1000)->nullable();
            $table->unsignedInteger('institute_id')->index('training_centers_fk_institute_id');
            $table->unsignedInteger('branch_id')->nullable()->index('training_centers_fk_branch_id');
            $table->text('address')->nullable();
            $table->text('google_map_src')->nullable();
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 -> inactive, 1 ->active');
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
        Schema::dropIfExists('training_centers');
    }
}
