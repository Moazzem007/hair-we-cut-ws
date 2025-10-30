<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class outsidemarketplace extends Controller
{
    public function marketpage(){
        return view('marketplace.marketplacepage');
    }
}
