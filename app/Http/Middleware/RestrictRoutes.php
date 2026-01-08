<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RestrictRoutes
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
        // Get the current route name
        $currentRoute = $request->route()->getName();

        // Get the URL of the previous route name from the session
        $previousUrl = Session::get('previous_url');

        if ($currentRoute == 'logout') {
            Session::put('previous_url', $request->route()->getName());
            return $next($request);
        }
        // Check if the previous route is 'step5to5'
        if ($previousUrl == 'step5to5' && ($currentRoute == 'profile' || $currentRoute == 'phone_code_step' || $currentRoute == 'step3to5' || $currentRoute == 'step1to5')) {
            Session::put('previous_url', $request->route()->getName());
            return $next($request);
        }
        // Check if the previous route is 'step4to5'
        // if ($previousUrl == 'step3to5' && $currentRoute == 'step5to5') {
        //     Session::put('previous_url', $request->route()->getName());
        //     return $next($request);
        // }
        // Check if the current route is 'step5to5'
        if ($previousUrl == 'step5to5' && $currentRoute !== 'step5to5') {
            Session::put('previous_url', $request->route()->getName());
            // Allow access to the 'step5to5' route
            return redirect()->route('step5to5', ['lang' => $request->lang]);
        }
        // Check if the previous route is 'step3to5'
        if ($previousUrl == 'step3to5' && ($currentRoute == 'step5to5' || $currentRoute == 'step2to5')) {
            Session::put('previous_url', $request->route()->getName());
            return $next($request);
        }
        // Check if the current route is 'step4to5'
        // if ($previousUrl == 'step4to5' && $currentRoute !== 'step4to5') {
        //     Session::put('previous_url', $request->route()->getName());
        //     // Allow access to the 'step4to5' route
        //     return redirect()->route('step4to5', ['lang' => $request->lang]);
        // }
        // Check if the previous route is 'step2to5'
        if ($previousUrl == 'step2to5' && ($currentRoute == 'step3to5' || $currentRoute == 'step1to5')) {
            Session::put('previous_url', $request->route()->getName());
            return $next($request);
        }
        // Check if the current route is 'step3to5'
        if ($previousUrl == 'step3to5' && $currentRoute !== 'step3to5') {
            Session::put('previous_url', $request->route()->getName());
            // Allow access to the 'step3to5' route
            return redirect()->route('step3to5', ['lang' => $request->lang]);
        }
        // Check if the previous route is 'step1to5'
        if ($previousUrl == 'step1to5' && $currentRoute == 'step2to5') {
            Session::put('previous_url', $request->route()->getName());
            return $next($request);
        }
        // Check if the current route is 'step2to5'
        if ($previousUrl == 'step2to5' && $currentRoute !== 'step2to5') {
            Session::put('previous_url', $request->route()->getName());
            // Allow access to the 'step2to5' route
            return redirect()->route('step2to5', ['lang' => $request->lang]);
        }
        // Check if the current route is 'step1to5'
        if ($previousUrl == 'step1to5' && $currentRoute !== 'step1to5') {
            Session::put('previous_url', $request->route()->getName());
            // Allow access to the 'step1to5' route
            return redirect()->route('step1to5', ['lang' => $request->lang]);
        }

        // Store the current URL as the previous URL for the next request
        Session::put('previous_url', $request->route()->getName());
        
        return $next($request);
    }
}
