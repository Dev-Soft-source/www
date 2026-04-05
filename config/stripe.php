<?php

$country = strtoupper(substr((string) env('STRIPE_ACCOUNT_COUNTRY', 'CA'), 0, 2));
if (strlen($country) !== 2) {
    $country = 'CA';
}

$currency = strtolower((string) env('STRIPE_ACCOUNT_CURRENCY', 'cad'));
$currency = preg_replace('/[^a-z]/', '', $currency);
if (strlen($currency) !== 3) {
    $currency = 'cad';
}

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'account_country' => $country,
    'account_currency' => $currency,
    /** Override Stripe.js / Elements locale (e.g. fr-CA). Empty = derive from site language in Controller View::share. */
    'elements_locale' => env('STRIPE_ELEMENTS_LOCALE'),
];
