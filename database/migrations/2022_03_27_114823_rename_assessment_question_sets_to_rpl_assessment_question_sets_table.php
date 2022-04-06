<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAssessmentQuestionSetsToRplAssessmentQuestionSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('assessment_question_sets', 'rpl_assessment_question_sets');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('rpl_assessment_question_sets', 'assessment_question_sets');

    }
}
