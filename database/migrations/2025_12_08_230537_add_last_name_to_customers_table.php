<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastNameToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('customers', 'last_name')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('last_name')->nullable()->after('name');
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
        if (Schema::hasColumn('customers', 'last_name')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('last_name');
            });
        }
    }
}
