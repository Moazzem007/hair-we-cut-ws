<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
            'name' => 'Barber Khan','email' => 'info@barber.com','user_id' => '2','salon' => 'The Barber Salon','contact' => '0000000','slot'=>'2','img' =>null,'address' => 'prang','lat'=>'00','lng'=>'00','radius'=>'12','barber_type'=>'Mobile Barber',
            'account_title'=>'Barber Khan','account_no'=>'12312412','credit_card' => '23423423'
        );
        Barber::create($barber);

        

        BarberTimeSlot::create([
            'slot_no' => 1,
            'from_time'=>'09:00',
            'to_time'=>'09:40',
            'barber_id'=>'2',
        ]);



    }
}
