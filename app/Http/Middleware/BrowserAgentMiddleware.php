<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BrowserAgentMiddleware
{
    public function handle($request, Closure $next)
    {
        $userAgent = $request->header('Browser-Agent');

        if (! $userAgent) {
            return response()->json(['message' => 'invalid_browser_agent'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
