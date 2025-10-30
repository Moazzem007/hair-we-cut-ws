<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('user_id');
            $table->string('salon');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('contact');
            $table->integer('slot')->default(1);
            $table->string('img')->nullable();
            $table->string('address');
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->integer('radius')->nullable();
            $table->string('barber_type')->nullable();
            $table->string('status')->default('Pendding'); // Active /
            $table->string('account_title')->nullable();
            $table->string('account_no')->nullable();
            $table->string('credit_card')->nullable();
            $table->string('device_token')->nullable();
            $table->boolean('is_business')->nullable()->default(true);
            $table->integer('barber_of')->nullable();
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
        Schema::dropIfExists('barbers');
    }
}
