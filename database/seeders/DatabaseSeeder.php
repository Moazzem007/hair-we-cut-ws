<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ServiceSeeder::class);
        $this->call(AuthSeeder::class);
        $this->call(CommissionSeeder::class);
        // \App\Models\User::factory(10)->create();
        // $this->call(barberSeeder::class);
    }
}
