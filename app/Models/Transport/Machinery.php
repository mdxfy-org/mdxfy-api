<?php

namespace App\Models\Transport;

use App\Models\Hr\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machinery extends Model
{
    use HasFactory;

    protected $table = 'transport.machinery';

    protected $fillable = [
        'user_id',
        'name',
        'model',
        'plate',
        'active',
    ];

    protected $attributes = [
        'active' => true,
    ];

    /**
     * Relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
