<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UniformSimulatorController extends Controller
{
    public function index()
    {
    	return view('uniform-simulator.index');
    }
}
