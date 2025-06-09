<?php

namespace App\Services;

use App\Models\Post\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PostService
{
    /**
     * Eager load answers for the post query.
     *
     * @return Builder<Post>
     */
    public function queryPublicPosts(bool $withAnswers = false): Builder
    {
        $post = Post::with([
            'user',
            'answersTo',
        ])
            ->withCount([
                'answers',
            ])
            ->where([
                ['visibility', '=', 'public'],
                ['as', '=', 'post'],
                ['active', '=', true],
            ])
        ;

        if ($withAnswers) {
            return $this->withAnswers($post);
        }

        return $post;
    }

    /**
     * Eager load answers for the post query.
     *
     * @param Builder<Post> $post
     *
     * @return Builder<Post>
     */
    public function withAnswers(Builder $post): Builder
    {
        return $post->with(['answers' => function ($query) {
            $query->with(['user', 'answersTo'])->withCount([
                'answers',
            ])->where([
                ['visibility', '=', 'public'],
                ['as', '=', 'post'],
                ['active', '=', true],
            ]);
        }, ]);
    }

    public function transformWithExcerpt(Collection $posts): Collection
    {
        return $posts->transform(fn ($post) => $this->applyExcerpt($post));
    }

    public function applyExcerpt(Post $post): Post
    {
        $lines = preg_split('/\r\n|\r|\n/', $post->content);
        $seeMore = false;

        if (count($lines) > 5) {
            $excerpt = implode("\n", array_slice($lines, 0, 8)).'...';
            $seeMore = true;
        } elseif (count($lines) === 1 && Str::length($post->content) > 250) {
            $excerpt = Str::limit($post->content, 250, '...');
            $seeMore = true;
        } else {
            $excerpt = implode("\n", $lines);
        }

        $post->content = $excerpt;
        $post->see_more = $seeMore;

        return $post;
    }
}
