<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_sections', function (Blueprint $table) {
            $table->char('uuid', 50);
            $table->unsignedInteger('exam_id');
            $table->unsignedInteger('question_type')
                ->comment("1=> MCQ , 2=>Fill in the Blanks, 3=>Yes/No,4=>Practical,5=>Field Work,6=>Presentation,7=>Descriptive");
            $table->unsignedInteger('number_of_questions')->default(0);
            $table->unsignedDecimal('total_marks')->default(0);
            $table->string('question_selection_type')->comment('1=> Fixed, 2=> Random from Question Bank, 3=> Random from Question Set');
            $table->string('row_status')->default(1);
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
        Schema::dropIfExists('exam_sections');
    }
}
