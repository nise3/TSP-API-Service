<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhysicalDisabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_disabilities', function (Blueprint $table) {
            $table->tinyIncrements("id");
            $table->char('code', 20);
            $table->string("title", 500);
            $table->string("title_en", 250)->nullable();
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
        Schema::dropIfExists('physical_disabilities');
    }
}
