<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function home() {
        $name = 'Win';
        $animals = [
            'dog',
            'cat',
            'lion',
        ];

        return view('homepage', [
            'name' => $name,
            'catName' => 'Dexter',
            'animals' => $animals,
        ]);
    }

    public function about() {
        return view('single-post');
    }
}
