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
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('exam_id')->nullable();
            $table->unsignedInteger('batch_id');
            $table->unsignedInteger('exam_type_id');
            $table->unsignedTinyInteger('type')->comment('1=>online, 2=>offline,3=>mixed,4=>Practical,5=>Field Work,6=>Presentation,7=>Assignment,8=>Assignment');
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
