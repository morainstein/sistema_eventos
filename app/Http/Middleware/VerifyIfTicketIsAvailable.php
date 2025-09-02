<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyIfTicketIsAvailable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $batch = $request->route('batch');

        $ticketIsAvailable = $batch->tickets_sold < $batch->tickets_qty
            ? true
            : false;
        
        if(!$ticketIsAvailable) {
            return response()->json(['message' => 'No tickets available'], 400);
        }

        $request->attributes->set("batch",$batch);
        
        return $next($request);
    }
}
