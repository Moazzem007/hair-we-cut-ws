<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarberTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barber_time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_no');
            $table->integer('barber_id');
            // $table->integer('salon_id');
            $table->time('from_time');
            $table->time('to_time');
            $table->string('status')->default('Avalible'); // Reserved
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
        Schema::dropIfExists('barber_time_slots');
    }
}
