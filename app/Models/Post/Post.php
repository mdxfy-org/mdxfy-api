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
 * @property string $uuid
 * @property string $content
 * @property string $as
 * @property string $visibility
 * @property int    $answer_to
 * @property string $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $inactivated_at
 */
class Post extends DynamicQuery
{
    use HasFactory;

    protected $table = 'post.post';

    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'content',
        'as',
        'visibility',
        'answer_to',
        'user_id',
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
        'password',
        'remember_token',
        'email_two_factor_auth',
        'email_verified',
        'email_verified_at',
        'number_two_factor_auth',
        'number_verified',
        'number_verified_at',
        'active',
        'inactivated_at',
    ];

    /**
     * Defines the relationship to the User model.
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
