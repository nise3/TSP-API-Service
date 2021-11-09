<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEduBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_boards', function (Blueprint $table) {
            $table->tinyIncrements("id");
            $table->string('code', 50);
            $table->string("title_en", 250);
            $table->string("title", 500);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE `edu_boards` comment 'Education board or national authority'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edu_boards');
    }
}
