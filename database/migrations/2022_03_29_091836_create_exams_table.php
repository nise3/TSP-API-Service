<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->comment('1=>offline, 2=>online, 3=>mixed');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('subject_id');
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('venue')->nullable();
            $table->unsignedDecimal('total_marks');
            $table->string('accessor_type')->nullable();
            $table->unsignedInteger('accessor_id');
            $table->string('purpose_name')->nullable();
            $table->unsignedInteger('purpose_id');
            $table->unsignedTinyInteger('row_status');
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
        Schema::dropIfExists('exams');
    }
}
