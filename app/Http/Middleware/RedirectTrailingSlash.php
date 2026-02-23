<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectTrailingSlash
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $path = $request->getPathInfo();

        if ($path !== '/' && str_ends_with($path, '/')) {
            $normalizedPath = rtrim($path, '/');
            $target = $request->getSchemeAndHttpHost() . $normalizedPath;

            if ($request->getQueryString()) {
                $target .= '?' . $request->getQueryString();
            }

            return redirect()->to($target, 301);
        }

        return $next($request);
    }
}
