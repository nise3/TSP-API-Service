<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamSectionQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_section_questions', function (Blueprint $table) {
            $table->char('uuid', 50);
            $table->char('exam_set_uuid', 50)->nullable();
            $table->char('exam_section_uuid', 50);
            $table->string('question_selection_type')->comment('1=> Fixed, 2=> Random from Question Bank, 3=> Random from Question Set');
            $table->unsignedDecimal('individual_marks')->default(0);
            $table->unsignedInteger('question_id');
            $table->text('title');
            $table->text('title_en')->nullable();
            $table->string('accessor_type', 100)->nullable();
            $table->unsignedInteger('accessor_id');
            $table->unsignedInteger('subject_id')->index('qb_fk_subject');
            $table->unsignedInteger('question_type')
                ->comment("1=> MCQ , 2=>Fill in the Blanks, 3=>Yes/No,4=>Practical,5=>Field Work,6=>Presentation,7=>Descriptive");
            $table->string('option_1', 600)->nullable();;
            $table->string('option_1_en', 300)->nullable();;
            $table->string('option_2', 600)->nullable();;
            $table->string('option_2_en', 300)->nullable();;
            $table->string('option_3', 600)->nullable();;
            $table->string('option_4', 600)->nullable();;
            $table->string('option_3_en', 300)->nullable();;
            $table->string('option_4_en', 300)->nullable();;
            $table->json('answers');
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
        Schema::dropIfExists('exam_section_questions');
    }
}
