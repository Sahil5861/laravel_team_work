<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function sidebar()
    {
        return view('sidebar');
    }
    public function sidebar_component()
    {
        return view('sidebar-component');
    }

}
