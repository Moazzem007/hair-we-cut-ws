<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketproducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketproducts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_creater')->unsigned();
            // $table->foreign('job_creater')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('product_name')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('price')->nullable();
            $table->string('discountprice')->nullable();
            $table->string('shift_cost')->nullable();
            $table->string('short_description')->nullable();
            $table->string('detail_description')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->string('specification')->nullable();
            $table->string('contactname')->nullable();
            $table->string('contactnumbar')->nullable();
            $table->string('contactemail')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('marketproducts');
    }
}
