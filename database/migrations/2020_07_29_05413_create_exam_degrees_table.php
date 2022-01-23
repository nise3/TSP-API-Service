<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamDegreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_degrees', function (Blueprint $table) {
            $table->smallIncrements("id");
            $table->unsignedMediumInteger('education_level_id')
                ->comment('1=> PSC/5 Pass, 2=> JSC/JDC/8 Pass, 3=> Secondary, 4=> Higher Secondary, 5=> Diploma, 6=> Bachelor/Honors, 7=> Masters, 8=> PhD');
            $table->string("code" , 50);
            $table->string("title_en" , 255);
            $table->string("title", 500);
            $table->unsignedTinyInteger("row_status")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('education_level_id')
                ->references('id')
                ->on('education_levels')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_degrees');
    }
}
