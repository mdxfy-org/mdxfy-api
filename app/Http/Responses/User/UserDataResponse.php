<?php

namespace App\Http\Responses\User;

use App\Models\Hr\User;

class UserDataResponse
{
    /**
     * Format the user data for the response.
     *
     * @param User $user
     * @return array
     */
    public static function format(User $user): array
    {
        return [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'number' => $user->number,
            'profile_picture' => $user->profile_picture,
            'profile_banner' => $user->profile_banner,
        ];
    }
}
