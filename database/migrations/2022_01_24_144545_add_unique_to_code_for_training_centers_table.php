<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToCodeForTrainingCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_centers', function (Blueprint $table) {
            $table->unique('code',"training_center_code_unique_indexing");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_centers', function (Blueprint $table) {
            $table->dropUnique('code');
        });
    }
}
