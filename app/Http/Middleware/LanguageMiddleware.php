<?php

namespace App\Http\Middleware;

use App\Models\Hr\User;
use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $user = User::auth();
        if ($user instanceof User) {
            try {
                $candidate = Locale::format($user->language);
            } catch (\Exception $e) {
                $candidate = Locale::format(config('app.fallback_locale', 'en'));
            }
        } else {
            $raw = $request->header('Accept-Language')
            ?: $request->input('language');

            if ($raw) {
                $candidate = Locale::format(explode(',', $raw)[0]);
            } else {
                $candidate = Locale::format(config('app.fallback_locale', 'en'));
            }
        }

        $locale = Locale::resolve($candidate);

        App::setLocale($locale);

        return $next($request);
    }
}
