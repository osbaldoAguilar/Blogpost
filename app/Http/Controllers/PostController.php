<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function showCreateForm()
    {
        return view('create-post');
    }

    public function showEditForm(Post $post)
    {
        // $post['body'] = strip_tags((Str::markdown($post->body)), '<h1><h2><h3><p><ul><li><strong><em><br>');
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

        return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');
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
}
