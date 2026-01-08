<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Traits\StatusResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class Authenticate
{
    use StatusResponser;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the current route name
        $currentRouteName = $request->route()->getName();
        if (Str::startsWith($currentRouteName, 'app.')) {
            if (Auth::guard('sanctum')->check()) {
                $lang_id = Auth::guard('sanctum')->user()->lang_id;
                $selectedLanguage = Language::whereId($lang_id)->value('abbreviation');
                if ($selectedLanguage) {
                    $locale = $selectedLanguage;
                } else {
                    $locale = 'en';
                }
                
                App::setLocale($locale);

                return $next($request);
            }
            return $this->apiErrorResponse('Please signin to proceed', 401);
        }

        if (!Auth::guard('web')->check()) {
            return redirect(route('login', ['lang' => $request->lang]));
        }
        return $next($request);
    }

    // public function handle(Request $request, Closure $next, ...$guards)
    // {
    //     $currentRouteName = $request->route()->getName();

    //     if (Str::startsWith($currentRouteName, 'app.') && !in_array('sanctum', $guards)) {
    //         return $this->apiErrorResponse('Please sign in to proceed.', 401);
    //     }

    //     // Check for each guard provided
    //     foreach ($guards as $guard) {
    //         if (Auth::guard($guard)->check()) {
    //             // Authenticated with this guard, proceed
    //             return $next($request);
    //         }
    //     }

    //     // None of the guards were used for authentication
    //     return $this->apiErrorResponse('Unauthenticated.', 401);
    // }
}
