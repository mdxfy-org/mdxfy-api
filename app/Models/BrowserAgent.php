<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class BrowserAgent extends Model
{
    protected $table = 'hr.browser_agent';

    protected $fillable = [
        'user_agent',
        'fingerprint',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Create a new browser agent for the user.
     *
     * @return BrowserAgent
     */
    public static function createBrowserAgent(): self
    {
        return self::create([
            'user_agent' => request()->header('User-Agent'),
            'fingerprint' => Str::uuid(),
        ]);
    }
}
