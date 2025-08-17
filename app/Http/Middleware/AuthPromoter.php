<?php

namespace App\Http\Middleware;

use App\Models\Promoter;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthPromoter
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken();
        $user = PersonalAccessToken::findToken($plainTextToken)->tokenable ?? false;

        if (!$user || !$user instanceof Promoter) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
