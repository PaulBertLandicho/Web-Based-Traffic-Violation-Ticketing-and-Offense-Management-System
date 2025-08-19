<?php

namespace App\Http\Controllers;

class IntroController extends Controller

{
    public function welcome()
    {
        return view('welcome');
    }

    public function gov()
    {
        return view('gov');
    }
}
