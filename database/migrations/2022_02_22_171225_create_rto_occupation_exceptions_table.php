<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRtoOccupationExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rto_occupation_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('registered_training_organization_id')->index('rto_org_id_inx');
            $table->unsignedInteger('occupation_id')->index('rto_occupation_id_inx');
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
        Schema::dropIfExists('rto_occupation_exceptions');
    }
}
