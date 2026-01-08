<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CancellationHistory;
use App\Models\User;
use App\Models\Booking;
use App\Models\Ride;
use App\Models\SiteSetting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeactiveUserAccountCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactive-user-account:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $getUsers = User::where('admin_deactive_account', '0')->get();

        foreach ($getUsers as $key => $getUser) {
            $getDriverCancellationHistory = CancellationHistory::where('user_id', $getUser->id)->where('type', 'driver')->groupBy('ride_id')->count('id');
            if(isset($getDriverCancellationHistory) && $getDriverCancellationHistory != 0){
                $getUser->admin_deactive_account = '1';
                $getUser->save();
            }


            $getPassengerCancellationHistory = CancellationHistory::where('user_id', $getUser->id)->where('type', 'passenger')->groupBy('booking_id')->count('id');
            if(isset($getPassengerCancellationHistory) && $getPassengerCancellationHistory != 0){
                $getUser->admin_deactive_account = '1';
                $getUser->save();
            }

        }

        Log::info("Success");
    }
}
