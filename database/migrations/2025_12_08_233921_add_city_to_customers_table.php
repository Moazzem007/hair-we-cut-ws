<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('customers', 'city')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('city')->nullable(); 
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('customers', 'city')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('city');
            });
        }
    }
}
