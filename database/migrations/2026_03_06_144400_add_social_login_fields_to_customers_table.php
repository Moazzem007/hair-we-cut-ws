<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialLoginFieldsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('login_provider')->nullable()->after('facebook_id'); // 'google', 'facebook', or null for email/password
            $table->string('password')->nullable()->change();
            $table->string('contact')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'login_provider']);
            $table->string('password')->nullable(false)->change();
            $table->string('contact')->nullable(false)->change();
        });
    }
}
