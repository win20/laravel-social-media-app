<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function home() {
        return '<h1>Home Page!</h1><a href="/about">About page</a>';
    }

    public function about() {
        return '<h1>About Page</h1><a href="/">Home</a>';
    }
}
