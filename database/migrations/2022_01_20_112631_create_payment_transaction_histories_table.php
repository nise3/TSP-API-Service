<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transaction_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice');
            $table->string('mer_trnx_id')->unique();
            $table->string('trnx_id')->nullable();
            $table->unsignedInteger('payment_purpose_related_id');
            $table->unsignedTinyInteger('payment_purpose_code');
            $table->unsignedTinyInteger('payment_gateway_type')
                ->comment("1=Ek-Pay, 2=>SSLCOMMERZ, 2=> DBBL Mobile Banking, 3=>Bkash, 4=>PortWallet");
            $table->string('customer_identity_code');
            $table->string('customer_name', 500)->nullable();
            $table->string('customer_mobile', 15)->nullable();
            $table->string('customer_email', 150)->nullable();
            $table->unsignedDecimal('amount', 12, 4);
            $table->unsignedDecimal('paid_amount', 12, 4)->nullable();
            $table->string('trnx_currency')->comment('BDT');
            $table->json('request_payload')->nullable();
            $table->json('response_message')->nullable();
            $table->string('status')->default(2)->comment("1=>Success, 2=>Pending, 3=>Fail, 5=>Cancel");
            $table->dateTime("transaction_created_at");
            $table->dateTime("transaction_completed_at")->nullable();
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
        Schema::dropIfExists('payment_transaction_histories');
    }
}
