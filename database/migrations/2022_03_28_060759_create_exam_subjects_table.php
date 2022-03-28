<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 600);
            $table->string('title_en', 300)->nullable();
            $table->string('assessor_type', 100)->nullable();
            $table->integer('assessor_id')->nullable();
            $table->unsignedTinyInteger('row_status')
                ->default(1)
                ->comment('0 => Inactive, 1 => Approved, 2 => Pending, 3 => Rejected');
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
        Schema::dropIfExists('exam_subjects');
    }
}
