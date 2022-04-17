<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRtoBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rto_batches', function (Blueprint $table) {
            $table->id();
            $table->string('title', 600);
            $table->string('title_en', 300)->nullable();
            $table->unsignedInteger('institute_id')->comment('certificate authority id');
            $table->unsignedInteger('rpl_level_id');
            $table->unsignedInteger('rpl_occupation_id');
            $table->unsignedInteger('assessor_id')->nullable()->comment('assessor youth id');
            $table->unsignedTinyInteger('certification_status')->comment('1=>not submitted,2=>submitted,3=>certified,4=>not certified');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rto_batches');
    }
}
