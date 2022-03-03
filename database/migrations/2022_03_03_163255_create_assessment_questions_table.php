<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->unsignedInteger('assessment_id')->index('assessment_question_fk_assessment_id');
            $table->unsignedInteger('question_id')->index('assessment_question_fk_question_id');
            $table->text('title');
            $table->text('title_en')->nullable();
            $table->unsignedInteger('type')->comment("1=> MCQ , 2=> Yes/No");
            $table->unsignedInteger('subject_id')->index('qb_fk_subject');
            $table->string('option_1',600);
            $table->string('option_1_en',300);
            $table->string('option_2',600);
            $table->string('option_2_en',300);
            $table->string('option_3',600);
            $table->string('option_4',600);
            $table->string('option_3_en',300);
            $table->string('option_4_en',300);
            $table->unsignedInteger('difficulty_level')->default(1)->comment('1=> Easy, 2=> Medium, 3=> Hard');
            $table->unsignedInteger('answer');
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
        Schema::dropIfExists('assessment_questions');
    }
}
