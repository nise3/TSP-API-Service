<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusPaymentDateToRplApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rpl_applications', function (Blueprint $table) {
            $table->unsignedTinyInteger('payment_status')->nullable()->default(2)->after('score')->comment("1=>Success, 2=>Pending, 3=>Fail, 5=>Cancel");
            $table->dateTime('payment_date')->nullable()->after('score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rpl_applications', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_date');
        });
    }
}
