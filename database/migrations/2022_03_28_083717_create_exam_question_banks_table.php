<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamQuestionBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_question_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->text('title_en')->nullable();
            $table->string('accessor_type',100)->nullable();
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
        Schema::dropIfExists('exam_question_banks');
    }
}
