<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasColumn('customers', 'google_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('google_id')->nullable()->after('email');
                $table->string('facebook_id')->nullable()->after('google_id');
                $table->string('login_provider')->nullable()->after('facebook_id');
            });
        }

        // Use raw SQL to make existing columns nullable (avoids doctrine/dbal requirement)
        DB::statement('ALTER TABLE customers MODIFY password VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY contact VARCHAR(255) NULL');
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
        });

        DB::statement('ALTER TABLE customers MODIFY password VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY contact VARCHAR(255) NOT NULL');
    }
}
