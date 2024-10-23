<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function show()
    {
        if(auth()->user()){
            return view('dashboard');
        }
        else {
            return view('welcome');
        }
    }
}
