<?php

namespace App\Providers;

use App\View\Composers\BirthdayComposer;
use App\View\Composers\ErrorPageComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.template', BirthdayComposer::class);
        View::composer(['errors.404', 'errors/404'], ErrorPageComposer::class);
    }
}
