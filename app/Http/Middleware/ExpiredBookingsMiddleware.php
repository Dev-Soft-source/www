<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExpiredBookingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Expired bookings processing has been moved to the bookings:expire command
        // This middleware is kept lightweight to avoid blocking requests
        // The command should be scheduled to run periodically (e.g., every minute)
        
        return $next($request);
    }
}
