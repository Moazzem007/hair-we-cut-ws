<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // Elavon/Opayo transaction fields
            $table->string('transaction_type')->default('Payment');
            // Payment | Refund | Repeat | Void etc.

            $table->string('vendor_tx_code')->nullable();   // vendorTxCode sent by you
            $table->string('transaction_id')->nullable();   // returned by Opayo (transactionId)
            $table->string('status')->nullable();           // ok, declined, rejected, authenticated, registered etc.

            $table->integer('amount')->unsigned();          // Amount in pence (integer)
            $table->string('currency', 3)->default('GBP');

            $table->boolean('requires_3ds')->default(false);
            $table->string('acs_url')->nullable();          // for 3DS step
            $table->json('three_ds_data')->nullable();      // paReq + MD + postBack URLs

            $table->json('raw_request')->nullable();        // JSON payload you sent to Elavon
            $table->json('raw_response')->nullable();       // Full response from Elavon

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
        Schema::dropIfExists('payments');
    }
}
