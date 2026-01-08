<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RideCompleteCron::class,
        Commands\StudentCardExpiryCron::class,
        Commands\StudentAnnualRenewalCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('student-card-expiry:cron')->daily();
        $schedule->command('student-annual-renewal:cron')->daily();
        $schedule->command('delete-old-messages:cron')->daily();
        $schedule->command('user-birthday-wish:cron')->daily();
        $schedule->command('holiday-season:cron')->daily();
        $schedule->command('holiday-season:cron')->daily();
        $schedule->command('send-passenger-list:cron')->everyFifteenMinutes();

        $schedule->command('ride-complete:cron')
        ->everyThirtyMinutes();

        // $schedule->command('ride-complete:cron')
        // ->everySixHours();

        $schedule->command('backup:run --only-db')->daily()->at('01:30')
        ->onFailure(function () {
        })
        ->onSuccess(function () { 
            Log::info("Success");
        });

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
