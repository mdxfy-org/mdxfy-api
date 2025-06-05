<?php

namespace App\Http\Controllers;

use App\Factories\ResponseFactory;
use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Models\Hr\User;
use App\Models\Post\Post;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $offset = request()->query('offset', 0);

        $posts = Post::with('user')
            ->where('visibility', 'public')
            ->where('as', 'post')
            ->skip($offset)
            ->take(40)
            ->orderByDesc('created_at')
            ->get()
        ;

        if ($posts->isEmpty()) {
            return ResponseFactory::success('no_posts_found', []);
        }

        $posts->transform(function ($post) {
            $raw = $post->content;
            $lines = preg_split('/\r\n|\r|\n/', $raw);

            $seeMore = false;

            if (count($lines) > 6) {
                $slice = array_slice($lines, 0, 6);
                $excerpt = implode("\n", $slice).'...';
                $seeMore = true;
            } elseif (count($lines) === 1) {
                if (Str::length($raw) > 200) {
                    $excerpt = Str::limit($raw, 200, '...');
                    $seeMore = true;
                } else {
                    $excerpt = $raw;
                }
            } else {
                $joined = implode("\n", $lines);
                $excerpt = $joined;
            }

            $post->content = $excerpt;
            $post->see_more = $seeMore;

            return $post;
        });

        return ResponseFactory::success('posts_found', $posts);
    }

    public function show($uuid)
    {
        $post = Post::with('user')->where('uuid', $uuid)->first();

        if ($post) {
            return ResponseFactory::success('post_found', ['post' => $post]);
        }

        return ResponseFactory::error('post_not_found', null, null, 404);
    }

    public function userPosts($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return ResponseFactory::error('user_not_found', null, null, 404);
        }

        $offset = request()->query('offset', 0);

        $posts = Post::with('user')
            ->where('user_id', $user->id)
            ->skip($offset)
            ->take(40)
            ->orderByDesc('created_at')
            ->get()
        ;

        if ($posts->isEmpty()) {
            return ResponseFactory::success('no_posts_found', []);
        }

        return ResponseFactory::success('posts_found', $posts);
    }

    public function store(PostStoreRequest $request)
    {
        $user = User::auth();
        $data = $request->validated();

        $result = Post::create($data + [
            'uuid' => Str::uuid()->toString(),
            'user_id' => $user->id,
        ]);

        return ResponseFactory::success('post_created_successfully', $result, 201);
    }

    public function update(PostUpdateRequest $request) {}
}
