<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function delete(Request $request, Post $post)
    {
        if ($request->user()->cannot('delete', $post)) {
            return 'You cannot do that';
        }
        $post->delete();

        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post successfully deleted');
    }

    public function viewSinglePost(Post $post)
    {
        $post['body'] = Str::markdown($post->body);
        return view('single-post', ['post' => $post]);
    }

    public function showCreateForm()
    {
        return view('create-post');
    }

    public function storeNewPost(Request $request): RedirectResponse
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);

        return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');
    }
}
