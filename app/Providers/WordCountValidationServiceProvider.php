<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class WordCountValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('max_words', function ($attribute, $value, $parameters, $validator) {
            $maxWords = $parameters[0];
            $wordCount = str_word_count($value);
            return $wordCount <= $maxWords;
        });
    }
}
