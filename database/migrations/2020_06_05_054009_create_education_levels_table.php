<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education_levels', function (Blueprint $table) {
            $table->mediumIncrements("id");
            $table->string("code",20)
                ->comment('1=> PSC/5 Pass, 2=> JSC/JDC/8 Pass, 3=> Secondary, 4=> Higher Secondary, 5=> Diploma, 6=> Bachelor/Honors, 7=> Masters, 8=> PhD');
            $table->string("title_en", 250);
            $table->string("title", 500);
            $table->unsignedTinyInteger("row_status")->default(1);
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
        Schema::dropIfExists('education_levels');
    }
}
