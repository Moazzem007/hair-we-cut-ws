<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarberProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barber_products', function (Blueprint $table) {
            $table->id();
            $table->integer('barber_id');
            $table->integer('cat_id');
            $table->integer('product_id');
            $table->integer('admin_quantity')->default(0);
            $table->integer('barber_quantity')->default(0);
            $table->integer('status')->default(1);
            $table->string('pro_status');
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
        Schema::dropIfExists('barber_products');
    }
}
