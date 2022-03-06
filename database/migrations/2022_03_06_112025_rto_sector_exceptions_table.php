<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RtoSectorExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rto_sector_exceptions', function (Blueprint $table) {
            $table->unsignedInteger('registered_training_organization_id')->index('rto_sector_org_id_inx');
            $table->unsignedInteger('sector_id')->index('rto_sector_id_inx');
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
        Schema::dropIfExists('rto_sector_exceptions');

    }
}
