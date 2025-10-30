<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commission;
class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Commission::create([
            'percent' => 20,
            'product' => 10,
        ]);
    }
}
