<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('barber_id');
            $table->string('sender_type', 20); // customer | barber
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->timestamps();

            $table->index(['appointment_id', 'created_at']);
            $table->index(['customer_id', 'barber_id']);
            $table->index(['sender_type', 'sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}

