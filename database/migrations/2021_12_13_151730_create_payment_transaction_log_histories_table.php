<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionLogHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transaction_log_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
            $table->string('mer_trnx_id')->unique();
            $table->string('trnx_id')->nullable();
            $table->string('type');
            $table->unsignedTinyInteger('payment_gateway_type')->comment("1=Ek-Pay, 2=>SSLCOMMERZ, 2=> DBBL Mobile Banking, 3=>Bkash, 4=>PortWallet");
            $table->string("payment_instrument_type")->nullable()->comment('Payment Instrument Type');
            $table->string("payment_instrument_name")->nullable()->comment('Payment Instrument Name');
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->unsignedDouble('amount');
            $table->unsignedDouble('paid_amount')->nullable();
            $table->string('trnx_currency')->comment('BDT');
            $table->string('order_detail')->nullable();
            $table->json('request_payload');
            $table->json('response_message');
            $table->string('status')->default(2)->comment("1=>Success, 2=>Pending, 3=>Fail, 5=>Cancel");
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
        Schema::dropIfExists('payment_transaction_log_histories');
    }
}
