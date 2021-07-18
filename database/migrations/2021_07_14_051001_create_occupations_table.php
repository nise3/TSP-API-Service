<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccupationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occupations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_en');
            $table->string('title_bn');
            $table->unsignedInteger('job_sector_id')->index('occupations_fk_job_sector_id');
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->timestamps();
            $table->foreign('job_sector_id', 'occupations_fk_job_sector_id')->references('id')->on('job_sectors')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('occupations');
    }
}
