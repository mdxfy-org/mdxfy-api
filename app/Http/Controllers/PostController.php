<?php

namespace App\Http\Controllers;

use App\Factories\ResponseFactory;
use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Models\Hr\User;
use App\Models\Post\Post;
use App\Services\PostService;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    public function index()
    {
        $posts = $this->postService->queryPublicPosts()
            ->skip(request('offset', 0))
            ->take(40)
            ->orderByDesc('created_at')
            ->get()
        ;

        if ($posts->isEmpty()) {
            return ResponseFactory::success('no_posts_found', []);
        }

        $posts = $this->postService->transformWithExcerpt($posts);

        return ResponseFactory::success('posts_found', $posts);
    }

    public function show(string $uuid)
    {
        $post = $this->postService->queryPublicPosts(true)
            ->where('uuid', $uuid)
            ->first()
        ;

        if (!$post) {
            return ResponseFactory::error('post_not_found', null, null, 404);
        }

        if ($post->visibility !== 'public' && $post->user_id !== User::auth()->id) {
            return ResponseFactory::error('unauthorized_action', null, null, 403);
        }

        return ResponseFactory::success('post_found', $post);
    }

    public function userPosts(string $username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return ResponseFactory::error('user_not_found', null, null, 404);
        }

        $posts = $this->postService->queryPublicPosts()
            ->where('user_id', $user->id)
            ->skip(request('offset', 0))
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

        $data['uuid'] = Str::uuid()->toString();
        $data['user_id'] = $user->id;

        if (!empty($data['answer_to'])) {
            $answerToPost = Post::where('uuid', $data['answer_to'])->first();
            if ($answerToPost->visibility !== 'public' && $answerToPost->as !== 'post') {
                return ResponseFactory::error('unauthorized_action', null, null, 403);
            }

            $data['answer_to'] = $answerToPost->id;
        }

        $result = Post::create($data);

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
