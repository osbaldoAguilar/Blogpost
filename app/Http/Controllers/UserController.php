<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:25', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);
        // seems to be doing automatically
        // $incomingFields['password'] = bcrypt($incomingFields['password']);
        // looks in associated array for password
        $user = User::create($incomingFields);
        Auth::login($user);
        return redirect('/')->with('success', 'Thank you for creating an account!');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' =>  'required'
        ]);

        // Attempt to log in with the given username and password
        if (Auth::attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            // Login successful, redirect or return success message
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You are now successfully logged in');
        } else {
            // Login failed, return error message
            return redirect('/')->with('failure', 'Wow, that was so off it not even funny');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'You are now successfully logged out');;
    }

    public function showCorrectHomePage()
    {
        if (Auth::check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function showProfileView(User $user)
    {
        return view('profile-post', ['username' => $user->username, 'posts' => $user->posts()->get(), 'postCount' => $user->posts()->count()]);
    }
}
