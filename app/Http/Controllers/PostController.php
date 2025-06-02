<?php

namespace App\Http\Controllers;

use App\Factories\ResponseFactory;
use App\Http\Responses\User\UserDataResponse;
use App\Models\Hr\User;
use App\Models\Post\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $offset = request()->query('offset', 0);

        $posts = Post::with("user")->skip($offset)->take(40)->get();

        if ($posts->isEmpty()) {
            return ResponseFactory::error('no_posts_found', null, null, 404);
        }

        return ResponseFactory::success('posts_found', $posts);
    }

    public function show($uuid)
    {
        $post = Post::where('uuid', $uuid)->first();
        $user = $post->user()->first();

        if ($post) {
            return ResponseFactory::success('post_found', ['post' => $post, 'user' => UserDataResponse::format($user)]);
        }

        return ResponseFactory::error('post_not_found', null, null, 404);
    }

    public function userPosts($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return ResponseFactory::error('user_not_found', null, null, 404);
        }

        $posts = Post::where('user_id', $user->id)->get();

        if ($posts->isEmpty()) {
            return ResponseFactory::error('no_posts_found', null, null, 404);
        }

        return ResponseFactory::success('posts_found', ["user" => UserDataResponse::format($user), "posts" => $posts]);
    }

    public function store(Request $request)
    {
        $user = User::auth();
        $data = $request->validate([
            'as' => 'required|in:post,draft',
            'content' => 'required|string|max:5000',
            'visibility' => 'required|in:public,private,friends',
        ]);

        $result = Post::create($data + [
            'uuid' => Str::uuid()->toString(),
            'user_id' => $user->id,
        ]);

        return ResponseFactory::success('post_created_successfully', $result, 201);
    }

    public function update(Request $request)
    {

    }
}
