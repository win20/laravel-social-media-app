<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use App\Models\Follow;

class UserController extends Controller
{
    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = $request->user();

        $filename = $user->id . '-' . uniqid() . '.jpg';

        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != '/fallback-avatar.jpg') {
            Storage::delete(str_replace('/storage/', 'public/', $oldAvatar));
        }

        return back()->with('success', 'You have change your avatar');
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function profile(User $user)
    {
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }


        return view('profile-posts', [
            'avatar' => $user->avatar,
            'username' => $user->username,
            'posts' => $user->posts()->latest()->get(),
            'postCount' => $user->posts()->count(),
            'currentlyFollowing' => $currentlyFollowing,
        ]);
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in');
        } else {
            return redirect('/')->with('failure', 'Invalid login');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out');
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create($incomingFields);

        auth()->login($user);

        return redirect('/')->with('success', 'Thank you for creating an account');
    }
}
