<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Intervention\Image\Drivers\Gd\Driver;

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
            // return auth()->user()->feedUserPosts();
            // Osbaldo you had error here where you were trying to call the feedUserPosts
            // return view('homepage-feed', ['posts' => auth()->user()->feedUserPosts]);
            return view('homepage-feed', ['posts' => auth()->user()->feedUserPosts()->latest()->paginate(4)]);
        } else {
            return view('homepage');
        }
    }

    private function getSharedData($user)
    {
        $currentFollowing = 0;

        if (auth()->check()) {
            $currentFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['follows_user', '=', $user->id]])->count();
        }
        View::share('sharedData', ['currentFollowing' => $currentFollowing, 'avatar' => $user->avatar, 'username' => $user->username, 'postCount' => $user->posts()->count(), 'followersCount' => $user->followers()->count(), 'followingCount' => $user->following()->count()]);
    }


    public function showProfileView(User $user)
    {
        $this->getSharedData($user);
        return view('profile-post', ['posts' => $user->posts()->latest()->get()]);
    }
    public function showProfileFollowers(User $user)
    {
        $this->getSharedData($user);
        // return $user->followers()->get();
        return view('profile-followers', ['followers' => $user->followers()->get()]);
        // $currentFollowing = 0;

        // if (auth()->check()) {
        //     $currentFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['follows_user', '=', $user->id]])->count();
        // }


        // return view('profile-followers', ['currentFollowing' => $currentFollowing, 'avatar' => $user->avatar, 'username' => $user->username, 'posts' => $user->posts()->get(), 'postCount' => $user->posts()->count()]);
    }
    public function showProfileFollowing(User $user)
    {
        $this->getSharedData($user);
        return view('profile-following', ['following' => $user->following()->get()]);

        // $currentFollowing = 0;

        // if (auth()->check()) {
        //     $currentFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['follows_user', '=', $user->id]])->count();
        // }


        // return view('profile-following', ['currentFollowing' => $currentFollowing, 'avatar' => $user->avatar, 'username' => $user->username, 'posts' => $user->posts()->get(), 'postCount' => $user->posts()->count()]);
    }
    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function storeNewAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        // Create $variable for access to
        //  the user from authentication
        $user = auth()->user();
        // create $var for custom unique filenames 
        // read it to understand
        $filename = $user->id . '-' . uniqid() . '.jpg';

        // Leverage the intervention package to resize 
        // image before the image gets stored in the database
        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        $imgData = $image->cover(120, 120)->toJpeg();
        Storage::disk('public')->put('avatars/' . $filename, $imgData);
        // Storage::put('/public/storage/avatar.jpg', $imgData);

        $user->avatar = $filename;

        $oldAvatar = $user->avatar;

        $user->save();

        if ($oldAvatar !== '/fallback-avatar.jpg') {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Congrates on Avatar Change!!');




        // Basic way to save an image to private app folder
        // $request->file('avatar')->store('avatars');;
    }
}
