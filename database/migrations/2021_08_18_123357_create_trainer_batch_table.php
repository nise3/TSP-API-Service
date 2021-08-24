<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainerBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainer_batch', function (Blueprint $table) {
            $table->unsignedInteger('trainer_id');
            $table->unsignedInteger('batch_id');

            $table->foreign('trainer_id')
                ->references('id')
                ->on('trainers')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainer_batch');
    }
}
