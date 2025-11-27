<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->integer('amount')->unsigned(); // store pence/cents as integer
            $table->string('currency', 3)->default('GBP');
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('opayo_transaction_id')->nullable();
            $table->json('opayo_response')->nullable();
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
        Schema::dropIfExists('payment_orders');
    }
}
