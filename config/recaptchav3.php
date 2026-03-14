<?php

return [
    'origin' => env('RECAPTCHAV3_ORIGIN', 'https://www.google.com/recaptcha'),
    'sitekey' => env('RECAPTCHAV3_SITEKEY', env('GOOGLE_RECAPTCHA_KEY', '')),
    'secret' => env('RECAPTCHAV3_SECRET', env('GOOGLE_RECAPTCHA_SECRET', '')),
    'locale' => env('RECAPTCHAV3_LOCALE', ''),
];
