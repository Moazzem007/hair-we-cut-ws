<?php

namespace Database\Seeders;
use App\Models\Service;

use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Service::insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Hair Cut',
                'price' => '8',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Hair',
                'minut' => 40,
                'user_id' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Hair Styling',
                'price' => '9',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Hair',
                'minut' => 50,
                'user_id' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'Hair Triming',
                'price' => '10',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Hair',
                'minut' => 30,
                'user_id' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => 'Clean Shaving',
                'price' => '8',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Shaving',
                'minut' => 15,
                'user_id' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'title' => 'Beard Triming',
                'price' => '9',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Shaving',
                'minut' => 30,
                'user_id' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'title' => 'Smooth Shave',
                'price' => '10',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Shaving',
                'minut' => 20,
                'user_id' => 0,
            ),
            6 => 
            array (
                'id' => 7,
                'title' => 'White Facial',
                'price' => '10',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Face',
                'minut' => 40,
                'user_id' => 0,
            ),
            7 => 
            array (
                'id' => 8,
                'title' => 'Face Cleaning',
                'price' => '8',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Face',
                'minut' => 40,
                'user_id' => 0,
            ),
            8 => 
            array (
                'id' => 9,
                'title' => 'Bright Tuning',
                'price' => '9',
                'description' => 'Barber is a person whose occupation is mainly to cut dress groom style and shave men.',
                'type' => 'Face',
                'minut' => 50,
                'user_id' => 0,
            ),
        ));
    }
}
