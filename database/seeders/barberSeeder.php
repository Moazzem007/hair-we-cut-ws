<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Barber;
use App\Models\BarberTimeSlot;

class barberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = new User();
        $user->name = 'Barber Khan';
        $user->email = 'info@barber.com';
        $user->password = bcrypt('12345678');
        $user->type = 'Barber';
        $user->save();

        $barber = array(
            'name' => 'Barber Khan',
            'email' => 'info@barber.com',
            'user_id' => $user->id,
            'salon' => 'The Barber Salon',
            'contact' => '0000000',
            'slot'=>'2',
            'img' =>null,
            'address' => 'prang',
            'lat'=>'00',
            'lng'=>'00',
            'radius'=>'12',
            'barber_type'=>'Mobile Barber',
            'account_title'=>'Barber Khan',
            'account_no'=>'12312412',
            'credit_card' => '23423423',
            'status' => 'Active',
            'is_business' => true
        );
        Barber::create($barber);

        BarberTimeSlot::create([
            'slot_no' => 1,
            'from_time'=>'09:00',
            'to_time'=>'09:40',
            'barber_id'=>$user->id,
        ]);

        // Create an individual barber (not a business)
        $user2 = new User();
        $user2->name = 'Individual Barber';
        $user2->email = 'barber@example.com';
        $user2->password = Hash::make('12345678');
        $user2->type = 'Barber';
        $user2->save();

        $barber2 = array(
            'name' => 'Individual Barber',
            'email' => 'barber@example.com',
            'user_id' => $user2->id,
            'salon' => 'Individual',
            'contact' => '1111111',
            'slot'=>'1',
            'img' =>null,
            'address' => 'Street 1',
            'lat'=>'00',
            'lng'=>'00',
            'radius'=>'5',
            'barber_type'=>'Mobile Barber',
            'account_title'=>'Individual Barber',
            'account_no'=>'9999999',
            'credit_card' => '8888888',
            'status' => 'Active',
            'is_business' => false,
            'barber_of' => $user->id // Working for the first barber (salon)
        );
        Barber::create($barber2);



    }
}
