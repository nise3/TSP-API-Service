<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProgrammesTable
 */
class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->nullable();
            $table->unsignedInteger('institute_id');
            $table->string('title', 1000);
            $table->string('title_en', 500);
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->string('logo', 600)->nullable();
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
    function down()
    {
        Schema::dropIfExists('programs');
    }
}
