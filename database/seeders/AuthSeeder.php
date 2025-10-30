<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $row           = new User();
        $row->name     = 'Hair We Cut';
        $row->email    = 'admin@hairwecut.co.uk';
        $row->password = bcrypt('hairwecut');
        $row->type     = 'Admin';
        $row->save();
    }
}
