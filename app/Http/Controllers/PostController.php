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
                $slice = array_slice($lines, 0, 8);
                $excerpt = implode("\n", $slice).'...';
                $seeMore = true;
            } elseif (count($lines) === 1) {
                if (Str::length($raw) > 200) {
                    $excerpt = Str::limit($raw, 250, '...');
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
        $user = User::auth();
        // $post = Post::with('user')
        //     ->where('uuid', $uuid)
        //     ->where('as', 'post')
        //     ->where('visibility', 'public')
        //     ->where('active', true)
        //     ->first()
        // ;

        $post = Post::with('user')
            ->where('uuid', $uuid)
            // ->where('active', true)
            ->first()
        ;

        if ($post) {
            // if (($post->as === 'post' || $post->visibility !== 'public') && $post->user_id !== $user->id) {
            //     return ResponseFactory::error('post_not_found', null, null, 404);
            // }

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

        return ResponseFactory::success('posts_found', $posts);
    }

    public function store(PostStoreRequest $request)
    {
        $user = User::auth();

        $lastPost = Post::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first()
        ;

        if ($lastPost && $lastPost->created_at->diffInSeconds(now()) < 60 * 3) {
            return ResponseFactory::error('cannot_post_so_quickly', null, [
                'content' => __('post.cannot_post_so_quickly'),
            ], 429);
        }

        $data = $request->validated();

        $result = Post::create($data + [
            'uuid' => Str::uuid()->toString(),
            'user_id' => $user->id,
        ]);

        return ResponseFactory::success('post_created_successfully', $result, 201);
    }

    public function update(PostUpdateRequest $request)
    {
        $post = Post::where('uuid', $request->uuid)->first();

        if (!$post) {
            return ResponseFactory::error('post_not_found', null, null, 404);
        }

        if ($post->user_id !== User::auth()->id) {
            return ResponseFactory::error('unauthorized_action', null, null, 403);
        }

        $data = $request->validated();
        $post->update($data);

        return ResponseFactory::success('post_updated_successfully', $post);
    }

    public function delete($uuid)
    {
        $post = Post::where('uuid', $uuid)->first();

        if (!$post) {
            return ResponseFactory::error('post_not_found', null, null, 404);
        }

        if ($post->user_id !== User::auth()->id) {
            return ResponseFactory::error('unauthorized_action', null, null, 403);
        }

        $post->update([
            'active' => false,
            'inactivated_at' => now(),
        ]);

        return ResponseFactory::success('post_deleted_successfully', null, 204);
    }
}
