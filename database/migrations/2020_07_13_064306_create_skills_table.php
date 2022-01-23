<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     **/
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('title', 400);
            $table->string('title_en', 200)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     **/
    public function down()
    {
        Schema::dropIfExists('skills');
    }
}
