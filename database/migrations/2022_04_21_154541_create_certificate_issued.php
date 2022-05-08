<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateIssued extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_issued', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('certificate_id');
            $table->unsignedInteger('youth_id');
            $table->unsignedInteger('batch_id');
            $table->unsignedInteger('row_status');
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
        Schema::dropIfExists('certificate_issued');
    }
}
