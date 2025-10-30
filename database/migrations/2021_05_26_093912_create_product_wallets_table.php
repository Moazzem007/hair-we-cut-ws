<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('inv');
            $table->integer('barber_id');
            $table->integer('customer_id');
            $table->integer('order_id');
            $table->string('description');
            $table->integer('debit');
            $table->integer('credit');
            $table->float('com_amount');
            $table->string('pay_status')->default('UNPAID');
            $table->string('view')->default('unview');
            $table->string('barberview')->default('unview');
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
        Schema::dropIfExists('product_wallets');
    }
}
