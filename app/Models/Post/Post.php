<?php

namespace App\Models\Post;

use App\Models\DynamicQuery;
use App\Models\Hr\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Post.
 *
 * Represents a post in the system.
 *
 * @property int         $id
 * @property string      $uuid
 * @property string      $content
 * @property string      $visibility
 * @property int         $answer_to
 * @property string      $status
 * @property string      $user_id
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 */
class Post extends DynamicQuery
{
    use HasFactory;

    protected $table = 'post.post';

    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'content',
        'visibility',
        'answer_to',
        'status',
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Defines the relationship to the User model.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Post::class, 'answer_to', 'id');
    }

    public function answersTo()
    {
        return $this->belongsTo(Post::class, 'answer_to', 'id');
    }
}