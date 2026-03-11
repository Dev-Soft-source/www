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
        $routeLanguage = $request->route('lang');
        $queryLanguage = $request->query('lang');
        $userLanguage = Auth::guard('web')->check() ? Auth::guard('web')->user()->lang : null;

        $selectedLanguage = $routeLanguage
            ?: $queryLanguage
            ?: session('selectedLanguage')
            ?: $userLanguage
            ?: config('app.locale', 'en');

        if ($selectedLanguage) {
            session(['selectedLanguage' => $selectedLanguage]);
        }

        // Get the language from the session or default locale
        $locale = session('selectedLanguage', config('app.locale', 'en'));

        // Set the application locale
        App::setLocale($locale);

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if (($routeLanguage || $queryLanguage) && $user->lang !== $selectedLanguage) {
                $user->forceFill(['lang' => $selectedLanguage])->save();
            }

            if ($user->admin_deactive_account === '1') {
                Auth::guard('web')->logout();
                return redirect('/');
            }
        }

        return $next($request);
    }
}
