<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionCalendarController extends Controller
{
    public function index()
    {
        return view('production-calendar.index');
    }
}
