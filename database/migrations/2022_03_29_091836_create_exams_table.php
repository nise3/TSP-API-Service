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
            $table->unsignedTinyInteger('type')->comment('1=>online, 2=>offline,3=>mixed');
            $table->string('title', 500);
            $table->string('title_en', 250)->nullable();
            $table->unsignedInteger('subject_id');
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('venue')->nullable();
            $table->unsignedDecimal('total_marks')->default(0);
            $table->string('accessor_type', 150);
            $table->unsignedInteger('accessor_id');
            $table->string('purpose_name',150)->comment("example=> COURSE");
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
