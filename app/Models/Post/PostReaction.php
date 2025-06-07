<?php

namespace App\Models\Post;

use App\Models\DynamicQuery;
use App\Models\Hr\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Post.
 *
 * Represents a post in the system.
 *
 * @property int    $id
 * @property string $content
 * @property int    $user_id
 * @property int    $post_id
 * @property bool   $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $inactivated_at
 */
class PostReaction extends DynamicQuery
{
    use HasFactory;

    protected $table = 'post.post_reaction';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'active',
        'created_at',
        'updated_at',
        'inactivated_at',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id',
    ];

    /**
     * Defines the relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reactTo()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
