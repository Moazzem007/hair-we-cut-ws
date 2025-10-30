<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnercreatejob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partnercreatejob', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_creater')->unsigned();
            // $table->foreign('job_creater')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('companyname')->nullable();
            $table->string('email')->nullable();
            $table->string('contactno')->nullable();
            $table->string('experience')->nullable();
            $table->string('salary')->nullable();
            $table->string('gate_no')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();

            $table->string('gender')->nullable();
            $table->string('employee_type')->nullable();
            $table->string('role')->nullable();
            $table->string('vacancies')->nullable();
            $table->string('job_description')->nullable();

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
        Schema::dropIfExists('partnercreatejob');
    }
}
