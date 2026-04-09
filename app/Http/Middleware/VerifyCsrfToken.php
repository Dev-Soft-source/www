<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'stripe/*',
        'twilio/callback',
        'twilio/callback/conservation',
        // Apple OAuth callback is a server-side POST (form_post); CSRF token is not sent by Apple.
        '*/signup/*/callback',
        'signup/*/callback',
    ];
}
