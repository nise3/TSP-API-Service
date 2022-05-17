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
            $table->unsignedTinyInteger('type')->comment('1=>online, 2=>offline,3=>mixed,4=>Practical,5=>Field Work,6=>Presentation,7=>Assignment');
            $table->string('title', 500);
            $table->string('title_en', 250)->nullable();
            $table->unsignedInteger('subject_id');
            $table->string('accessor_type', 150);
            $table->unsignedInteger('accessor_id');
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('published_at')->nullable();
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
