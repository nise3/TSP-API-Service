<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->comment('1=>online, 2=>offline,3=>mixed');
            $table->string('title', 500);
            $table->string('title_en', 250)->nullable();
            $table->unsignedInteger('subject_id');
            $table->string('accessor_type', 150);
            $table->unsignedInteger('accessor_id');
            $table->unsignedTinyInteger('row_status')->default(1);
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
        Schema::dropIfExists('exam_types');
    }
}
