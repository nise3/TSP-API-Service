<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRplApplicationProfessionalQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rpl_application_professional_qualifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rpl_application_id');
            $table->unsignedInteger('rto_country_id');
            $table->unsignedInteger('rpl_sector_id');
            $table->unsignedInteger('rpl_occupation_id');
            $table->unsignedInteger('rpl_level_id');
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
        Schema::dropIfExists('rpl_application_professional_qualifications');
    }
}
