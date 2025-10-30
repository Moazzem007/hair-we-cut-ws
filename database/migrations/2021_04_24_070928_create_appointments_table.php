<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('barber_id');
            $table->integer('customer_id');
            $table->integer('salon_id');
            $table->integer('slote_id');
            $table->string('service_type'); // Adult / childe
            $table->string('address');
            $table->double('lat');
            $table->double('lng');
            $table->integer('service_id');
            $table->float('amount');
            $table->string('appType');
            $table->string('status')->default('Pendding'); // Completed // Canceled // Paided
            $table->string('stripe_token')->nullable();
            $table->boolean('refund')->default(false); //
            $table->boolean('cancel_payment')->default(false); // Deduction of cancelation payment
            $table->string('view')->default('unview');
            $table->string('barberview')->default('unview');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
