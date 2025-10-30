<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketsalonsell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketsalonsell', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_creater')->unsigned();
            // $table->foreign('job_creater')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('salon_name')->nullable();
            $table->string('address')->nullable();
            $table->string('barber_available')->nullable();
            $table->string('price')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('marketsalonsell');
    }
}
