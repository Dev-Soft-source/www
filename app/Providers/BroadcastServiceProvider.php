<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Web routes (for website chat via Laravel Echo)
        Broadcast::routes(['middleware' => ['web']]);

        // API routes (for mobile/API clients if needed)
        Broadcast::routes([
            'prefix' => 'api',
            'middleware' => ['api', 'auth:sanctum']
        ]);

        require base_path('routes/channels.php');
    }
}
