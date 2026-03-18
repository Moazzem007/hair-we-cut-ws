<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppleIdToCustomersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('customers', 'apple_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('apple_id')->nullable()->after('facebook_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('customers', 'apple_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('apple_id');
            });
        }
    }
}
