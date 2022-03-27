<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentQuestionSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_question_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('assessment_id');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->unsignedTinyInteger('row_status')
                ->default(1)
                ->comment('0 => Inactive, 1 => Active');
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
        Schema::dropIfExists('assesment_question_sets');
    }
}
