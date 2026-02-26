<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoCacheMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * It used in checkout pages to prevent caching of the page, ensuring that users always see the most up-to-date information when they access the checkout process.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);

        return $response->header('Cache-Control','no-cache, no-store, must-revalidate')
            ->header('Pragma','no-cache')
            ->header('Expires','0');
    }
}
