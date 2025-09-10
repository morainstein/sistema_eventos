<?php

namespace App\Http\Middleware;

use App\Models\Promoter;
use App\Models\Ticket;
use App\Services\TicketService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $signature = $request->header('signature');
        if(!$signature) {
            return response()->json(['message' => "header 'signature' has not been provided"], 401);
        };

        $promoterCredentials = Promoter::findPromoterByTicketsId($request->external_id)
            ->credentials;

        $verifyingHash = hash_hmac('sha256', $request->getContent(), $promoterCredentials->webhook_token);
        
        $isValid = hash_equals($signature,$verifyingHash);
        
        if(!$isValid){
            return response()->json(['message' => "header 'signature' is invalid"], 401);
        }

        return $next($request);
    }

}
