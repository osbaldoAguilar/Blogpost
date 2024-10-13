<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    //Define constraints

    public function createFollow(User $user)
    {
        // cant follow yourself
        if ($user->id == auth()->user()->id) {
            return back()->with('failure', "Oof! You can't follow yourself!");
        };
        // cant follow someone who is already followed
        $checkExist = Follow::where([['user_id', '=', auth()->user()->id], ['follows_user', '=', $user->id]])->count();

        if ($checkExist) {
            return back()->with('failure', "Oof! You're already following that user");
        };

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->follows_user = $user->id;
        //  My version less optimal 
        // if ($newFollow->user_id === $newFollow->follows_user) {
        //     return 'Cant Follow yourself';
        // }

        $newFollow->save();

        return back()->with('success', "You're currently following {$user->username}");
    }
    public function removeFollow(User $user)
    {

        // cant remove someone you are not follow

        // same logic cant remove someone that does not exist
        Follow::where([['user_id', '=', auth()->user()->id], ['follows_user', '=', $user->id]])->delete();
        return back()->with('success', "You're no longer following {$user->username}");
    }
}
