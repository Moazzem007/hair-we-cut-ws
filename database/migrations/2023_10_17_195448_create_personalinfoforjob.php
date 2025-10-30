<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalinfoforjob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personalinfoforjob', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_creater')->unsigned();
            $table->string('name')->nullable();
            $table->string('contect')->nullable();
            $table->string('email')->nullable();
            $table->string('gateno')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('experiencebarber')->nullable();
            $table->string('previewsalonname')->nullable();
            $table->string('presalonaddress')->nullable();
            $table->string('fromdate')->nullable();
            $table->string('todate')->nullable();
            $table->string('position/role')->nullable();
            $table->string('reasonforleaving')->nullable();
            $table->string('barber_licence_no')->nullable();
            $table->string('institute_name')->nullable();
            $table->string('institute_address')->nullable();
            $table->string('certificate/training')->nullable();
            $table->string('skill')->nullable();
            $table->string('available')->nullable();

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
        Schema::dropIfExists('personalinfoforjob');
    }
}
