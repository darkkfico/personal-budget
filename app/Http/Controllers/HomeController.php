<?php

namespace App\Http\Controllers;


class HomeController extends Controller
{
    public function index()
    {
        return view("home.index");
    }

    public function dashboard(){
        return view("start.dashboard");
    }

    public function start(){
        return view("start.start");
    }

}
