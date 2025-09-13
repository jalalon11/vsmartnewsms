<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Display the about us page
     */
    public function aboutUs()
    {
        return view('about-us');
    }
}