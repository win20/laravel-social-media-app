<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        User::create($incomingFields);
        return 'Hello from register';
    }
}
