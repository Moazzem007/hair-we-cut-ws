<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barber;


class Qrcodecontroller extends Controller
{
    public function qrcode($id){
        $data = Barber::where('user_id',$id)->get();
        return response()->json($data);
    }
}
