<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\PhoneNumber;
use App\Models\Ride;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class SendSmsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-sms:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send passenger  list sms to drvier before depature of ride';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour(); 
        
        $getAllRides = Ride::with('driver')->where('status','0')->whereBetween('ride_time', [$oneHourLater->copy()->subMinute(), $oneHourLater->copy()->addMinute()])->get();

        foreach ($getAllRides as $key => $ride) {
            $getBookings = Booking::with('passenger')->where('ride_id', $getRide->id)->where('status', '!=', '4')
            ->where('status', '!=', '3')->where('status', '!=', '0')->get();

            $messageContent = "";
            if(isset($getBookings) && count($getBookings) > 0){
                foreach ($getBookings as $key => $getBooking) {
                    if($messageContent == ""){
                        $messageContent = "".$getBooking->passenger->first_name."(".$getBooking->passenger->phone.")";
                    }else{
                        $messageContent .= "\n".$getBooking->passenger->first_name."(".$getBooking->passenger->phone.")";
                    }
                }
                $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->where('default', '1')->first();

                if (!$phoneNumber) {
                    $phoneNumber = PhoneNumber::where('user_id', $ride->added_by)->where('verified', '1')->first();
                }
                if ($phoneNumber && env('APP_ENV') != 'local' && isset($ride->driver->sms_notification) && $ride->driver->sms_notification==1) {
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_PHONE_NUMBER');
                
                    $twilio = new Client($sid, $token);
                    $to = $phoneNumber->phone;
                    
                    $title = "";
                    $currentHour = date('H');
                    if ($currentHour >= 0 && $currentHour < 12) {
                        $title = "Good morning ".$ride->driver->first_name."";
                    } elseif ($currentHour >= 12 && $currentHour < 17) {
                        $title = "Good afternoon ".$ride->driver->first_name."";
                    } else {
                        $title = "Good evening ".$ride->driver->first_name."";
                    }
    
                    $depatureDate = date('d F, Y H:i:s', strtotime(''.$ride->date.' '.$ride->time.''));
    
                    $message = "".$title."\nTrip detail\nOrigin: ".$booking->departure."\nDestination: ".$booking->destination."\nDeparture date: ".$depatureDate."\nHere is your passengers’ list\n".$messageContent."";
                
                    try {
                        $res = $twilio->messages->create(
                            $to,
                            [
                                'from' => $from,
                                'body' => $message,
                            ]
                        );
                    } catch (\Exception  $e) {
                        Log::info('can not send text to ' . $to . ' and message is ' . $message . ' because ' . $e->getMessage());
                    }
                }
            }
        }

        Log::info("Success");
    }
}
