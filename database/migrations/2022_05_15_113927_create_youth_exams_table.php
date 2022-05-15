<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYouthExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youth_exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('exam_id');
            $table->unsignedInteger('youth_id');
            $table->unsignedTinyInteger('type')->comment('1=>online, 2=>offline,3=>mixed');
            $table->json('file_paths')->nullable();
            $table->unsignedDecimal('total_obtained_marks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youth_exams');
    }
}
