<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Mail\NewPostEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNewPostEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    public function showCreateForm()
    {
        return view('create-post');
    }

    public function showEditForm(Post $post)
    {
        return view('edit-post', ['post' => $post]);
    }

    public function storeNewPost(Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = Auth::id();

        $newPost = Post::create($incomingFields);

        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

        return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');
    }
    public function storeNewPostApi(Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = Auth::id();

        $newPost = Post::create($incomingFields);

        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

        return $newPost->id;
        // redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');
    }

    public function showSinglePostView(Post $post)
    {
        $post['body'] = strip_tags((Str::markdown($post->body)), '<h1><h2><h3><p><ul><li><strong><em><br>');

        return view('single-post', ['post' => $post]);
    }

    public function delete(Post $post)
    {
        $post->delete();

        return redirect('/profile/' . auth()->user()->username)->with('success', "{$post->title} post was deleted");
    }
    // WIP - need to update the delete api function 
    // public function deletePostApi(Request $request, Post $post)
    // {
    //     // Authenticate the user using the bearer token
    //     $user = Auth::guard('sanctum')->user();

    //     if (!$user) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     // Check if the authenticated user is the owner of the post
    //     if ($post->user_id !== $user->id) {
    //         return response()->json(['error' => 'Forbidden'], 403);
    //     }

    //     // Delete the post
    //     $post->delete();

    //     return response()->json(['success' => "{$post->title} post was deleted"], 200);
    // }

    public function update(Post $post, Request $request)
    {

        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);


        $post->update($incomingFields);

        return redirect("/post/{$post->id}" . auth()->user()->username)->with('success', "{$post->title} was update");
    }

    public function search($term)
    {
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }
}
