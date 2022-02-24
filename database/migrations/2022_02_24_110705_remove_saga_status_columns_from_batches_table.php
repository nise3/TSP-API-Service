<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSagaStatusColumnsFromBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('saga_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->unsignedTinyInteger('saga_status')
                ->default(1)
                ->comment('1 => create_pending, 2 => update_pending, 3 => destroy_pending, 4 => commit, 5 => rollback')
                ->after('row_status');
        });
    }
}
