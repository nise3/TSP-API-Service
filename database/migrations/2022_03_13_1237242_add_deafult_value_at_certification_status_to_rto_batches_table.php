<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDeafultValueAtCertificationStatusToRtoBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rto_batches', function (Blueprint $table) {
            DB::statement('ALTER TABLE rto_batches ALTER certification_status SET DEFAULT 1');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rto_batches', function (Blueprint $table) {
            //
        });
    }
}
