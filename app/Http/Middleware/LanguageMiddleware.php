<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LanguageMiddleware
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
        $selectedLanguage = $request->query('lang');
    
        if ($selectedLanguage) {
            session(['selectedLanguage' => $selectedLanguage]);
        }

        // Get the language from the session or default to 'en'
        $locale = session('selectedLanguage', 'en');

        // Set the application locale
        App::setLocale($locale);

        if (Auth::guard('web')->check()) {
            if (Auth::guard('web')->user()->admin_deactive_account === '1') {
                Auth::guard('web')->logout();
                return redirect('/');
            }
        }

        return $next($request);
    }
}
