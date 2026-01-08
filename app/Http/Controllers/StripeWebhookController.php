<?php

namespace App\Http\Controllers;

use App\Models\CoffeeWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Twilio\TwiML\MessagingResponse;
use Twilio\Rest\Client;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        // Log request to check received data
        Log::info('Stripe Webhook Received:', $payload);

        if ($payload['type'] == 'invoice.payment_succeeded') {
            $userId = $payload['data']['object']['customer']; // Stripe customer ID
            $amount = $payload['data']['object']['amount_paid'] / 100;
            $transactionId = $payload['data']['object']['id'];

            CoffeeWallet::create([
                'name' => null,
                'email' => null,
                'phone' => null,
                'anonymous' => 0,
                'designation' => null,
                'package_id' => null,
                'frequency' => null,
                'dr_amount' => $amount,
                'paypal_id' => null,
                'stripe_id' => isset($transactionId) ? $transactionId : null,
                'subscription_id' => isset($userId) ? $userId : null,
                'payment_method' => 'stripe',
                'status' => 'completed',
            ]);
        } elseif ($payload['type'] == 'invoice.payment_failed') {
            // 
        }

        return response()->json(['status' => 'success']);
    }


    public function handleTwilloCallback(Request $request){

        Log::info('Twillo-Test');
        Log::info($request);
        return;

        $messageBody = $request->input('Body');
        $from = $request->input('From');

        $response = new MessagingResponse();

        if (trim($messageBody) == '33') {
            Log::info('Twillo-33');
            //$response->message("Call accepted, thank you for your response.");
        } elseif (trim($messageBody) == '11') {
            Log::info('Twillo-11');
            //$response->message("Call rejected, thank you for your response.");
        } else {
            Log::info('Twillo-invalid');
            //$response->message("Invalid response, please reply with 33 to accept or 11 to reject.");
        }

        Log::info('Twillo-not found');
    }


    public function handleTwilloCallbackv1(Request $request){

        Log::info('Twillo-Test');

        Log::info($request);
        return;

        $messageBody = $request->input('Body');
        $from = $request->input('From');

        $response = new MessagingResponse();

        if (trim($messageBody) == '33') {
            Log::info('Twillo-33');
            //$response->message("Call accepted, thank you for your response.");
        } elseif (trim($messageBody) == '11') {
            Log::info('Twillo-11');
            //$response->message("Call rejected, thank you for your response.");
        } else {
            Log::info('Twillo-invalid');
            //$response->message("Invalid response, please reply with 33 to accept or 11 to reject.");
        }

        Log::info('Twillo-not found');
    }


    public function handleTwilloConservationCallback(Request $request){

        Log::info('Twillo-Test-convservation');

        Log::info($request);
        
        $conversationSid = $request->input('ConversationSid');
        $messageBody = $request->input('Body');
        $attributes = json_decode($request->input('Attributes'), true);


        Log::info($conversationSid);
        Log::info($messageBody);
        Log::info($attributes);

        $from = $request->input('From');

        $response = new MessagingResponse();

        if (trim($messageBody) == '33') {
            Log::info('Twillo-33');
            //$response->message("Call accepted, thank you for your response.");
        } elseif (trim($messageBody) == '11') {
            Log::info('Twillo-11');
            //$response->message("Call rejected, thank you for your response.");
        } else {
            Log::info('Twillo-invalid');
            //$response->message("Invalid response, please reply with 33 to accept or 11 to reject.");
        }

        Log::info('Twillo-not found');
    }

    public function sendMessage(Request $request){


        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');

        

        $twilio = new Client($sid, $token);
        $to = "+15145577856";

        

        $message  = "for booking accept reply 33 and reject reply 11";

        $attributes = [
            'bookingId' => uniqid()
        ];

        
        Log::info($attributes);
    


            try {
                $conversationSid = null;
    
                $conversations = $twilio->conversations->v1->conversations->read();
            
                foreach ($conversations as $conversation) {
                    $participants = $twilio->conversations->v1
                        ->conversations($conversation->sid)
                        ->participants
                        ->read();
            
                    foreach ($participants as $participant) {
                        if (
                            isset($participant->messagingBinding['address']) &&
                            $participant->messagingBinding['address'] === $to
                        ) {
                            $conversationSid = $conversation->sid;
                            Log::info("Existing conversation found: " . $conversationSid);
                            break 2;
                        }
                    }
                }
            
                if (!$conversationSid) {
                    $conversation = $twilio->conversations->v1->conversations->create([
                        'friendlyName' => 'Chat with ' . $to,
                    ]);
                    $conversationSid = $conversation->sid;
                    $twilio->conversations->v1->conversations($conversationSid)
                        ->participants
                        ->create([
                            'messagingBinding.address' => $to,
                            'messagingBinding.proxyAddress' => $from,
                        ]);
            
                    Log::info("New conversation created: " . $conversationSid);
                }
            
                $twilio->conversations->v1->conversations($conversationSid)
                    ->messages
                    ->create([
                        'author' => 'system',
                        'body' => $message,
                        'attributes' => '{"testAttr": "hello"}',
                    ]);
            
                Log::info("Message sent to conversation: " . $conversationSid);
                
                
                //$messages = $twilio->conversations->v1->conversations($conversationSid)->messages->read();

                // $messages = $twilio->conversations->v1
                // ->conversations($conversationSid)
                // ->fetch();
                
                // Log::info('messages init');
                // Log::info($messages);
                // Log::info('messages complete');
            
            } catch (\Exception $e) {
                Log::error("Failed to send message to $to: " . $e->getMessage());
            }
        return "message send"; 
        
    }


    public function acceptRedirect($bookingId)
    {
        $deepLink = "xelentride://booking?booking_id={$bookingId}&action=accept";
        return response()->view('open-app', ['deepLink' => $deepLink]);
    }

    public function rejectRedirect($bookingId)
    {
        $deepLink = "xelentride://booking?booking_id={$bookingId}&action=reject";
        return response()->view('open-app', ['deepLink' => $deepLink]);
    }
}
