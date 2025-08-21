<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken();
        $user = PersonalAccessToken::findToken($plainTextToken)->tokenable ?? false;

        if (!$user || !$user instanceof Customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Auth::setUser($user);
        return $next($request);
    }
}
