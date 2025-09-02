<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignatureCashInPaggue
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(app()->isLocal()){
            return $next($request);
        }
       
        $signature = $request->header('signature');
        if(!$signature) {
            return response()->json(['message' => "header 'signature' has not been provided"], 401);
        };
        
        $verifyingHash = hash_hmac('sha256', $request->getContent(), env('PAGGUE_WEBHOOK_TOKEN'));
        
        $isValid = hash_equals($signature,$verifyingHash);
        
        if(!$isValid){
            return response()->json(status: 401);
        }

        return $next($request);
    }

}
