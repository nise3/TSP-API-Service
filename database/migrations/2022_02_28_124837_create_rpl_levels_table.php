<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRplLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rpl_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rpl_sector_id')->index('rpl_level_fk_rpl_sector_id');
            $table->unsignedInteger('rpl_occupation_id')->index('rpl_level_fk_rpl_occupation_id');
            $table->string('title', 600);
            $table->string('title_en', 300)->nullable();
            $table->json('translations')->nullable();
            $table->unsignedInteger('sequence_order');
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
        Schema::dropIfExists('rpl_levels');
    }
}
