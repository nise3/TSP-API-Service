<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_en', 191)->nullable();
            $table->string('title_bn', 191)->nullable();
            $table->unsignedInteger('loc_division_id')->nullable();
            $table->unsignedInteger('loc_district_id')->nullable();
            $table->unsignedInteger('loc_upazila_id')->nullable();
            $table->string('address', 191)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('fax_no', 50)->nullable();
            $table->string('contact_person_name', 191)->nullable();
            $table->string('contact_person_mobile', 20)->nullable();
            $table->string('contact_person_email', 191)->nullable();
            $table->string('contact_person_designation', 191)->nullable();
            $table->string('description')->nullable();
            $table->string('logo', 191)->nullable();
            $table->string('domain', 191)->nullable();
            $table->unsignedInteger('organization_type_id')->nullable()->index('organizations_fk_organization_type_id');
            $table->unsignedTinyInteger('row_status')->default(1);
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
        Schema::dropIfExists('organizations');
    }
}
