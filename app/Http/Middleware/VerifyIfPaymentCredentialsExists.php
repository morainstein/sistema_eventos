<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyIfPaymentCredentialsExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(!Auth::user()->credentials){
            $message = "There are no registered payment credentials. First, sign up the credentials so you can create an event.";
            return response()->json(['message' => $message],403);
        }


        return $next($request);
    }
}
