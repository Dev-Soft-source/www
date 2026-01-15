<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Mail\ReceiveChatMessageMail;
use App\Models\Booking;
use App\Models\Language;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ChatsPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\FCMToken;
use App\Models\Ride;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserMessageCount;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ChatsController extends Controller
{
    public function index($lang = null, $departure, $destination, $id, $passenger)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }

        $chatsPage = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $ride = Ride::whereId($id)->first();
        $passenger = User::whereId($passenger)->first();
        return view('chat', ['languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'notifications' => $notifications, 'ride' => $ride, 'passenger' => $passenger, 'chatsPage' => $chatsPage]);
    }

    public function chatDetail($lang = null, $id, $passenger)
    {
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $chatsPage = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $ride = Ride::whereId($id)->first();
        $passenger = User::whereId($passenger)->first();
        return view('chat_detail', ['languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'notifications' => $notifications, 'ride' => $ride, 'passenger' => $passenger, 'chatsPage' => $chatsPage]);
    }

    public function fetchMessages($id, $userId)
    {
        $user_id = auth()->user()->id;
        return Message::with('user', 'rideDetail')
            ->where('ride_id', $id)
            ->where(function ($query) use ($user_id, $userId) {
                $query->where('sender', $user_id)
                    ->where('receiver', $userId)
                    ->orWhere(function($q) use ($user_id, $userId) {
                        $q->where('sender', $userId)
                            ->where('receiver', $user_id);
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function fetchChats($userId)
    {
        $user_id = auth()->user()->id;
        return Message::with('user', 'rideDetail')
            ->where(function ($query) use ($user_id, $userId) {
                $query->where('sender', $user_id)
                    ->where('receiver', $userId)
                    ->orWhere('sender', $userId)
                    ->where('receiver', $user_id);
            })->where(function ($query) use ($user_id) {
                $query->whereNull('deleted_by')
                    ->orWhereRaw('FIND_IN_SET(?, deleted_by) = 0', [$user_id]); // ✅ Only get if user_id is NOT in deleted_by
            })
            ->get();
    }

    public function sendMessage(Request $request)
    {

        
        $selectedLanguage = session('selectedLanguage');
        $messages = null;
        if ($selectedLanguage) {
            
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('message_limit_exceeded_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('message_limit_exceeded_message')->first();
            }
        }

        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $contact_limit = SiteSetting::pluck('user_per_day_limit')->first();

        $contact_count = UserMessageCount::where('user_id', $user->id)
            ->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])
            ->first();

        if (is_null($contact_count) || $contact_count->user_inbox_count < $contact_limit) {
            $ride = Ride::whereId($request->ride_id)->with(['rideDetail' => function ($q) {
                $q->where('default_ride', '1');
            }])->first();

            $rideDetailId = "";
            if (isset($ride->rideDetail[0]) && !empty($ride->rideDetail[0])) {
                $rideDetailId = $ride->rideDetail[0]->id;
            }

            // Check the last message between the sender and receiver
            $lastMessage = Message::where(function ($query) use ($user, $request) {
                $query->where('sender', $user->id)
                    ->where('receiver', $request->userId);
            })->orWhere(function ($query) use ($user, $request) {
                $query->where('sender', $request->userId)
                    ->where('receiver', $user->id);
            })->latest()->first();

            // Check the ride first message
            $rideFirstMessage = Message::where(function ($query) use ($user, $request) {
                $query->where('sender', $user->id)
                    ->where('receiver', $request->userId);
            })->orWhere(function ($query) use ($user, $request) {
                $query->where('sender', $request->userId)
                    ->where('receiver', $user->id);
            })->where('ride_id', $ride->id)->first();

            if ($lastMessage) {
                $lastMessageTime = $lastMessage->created_at;
                $timeDifference = now()->diffInMinutes($lastMessageTime);

                if ($timeDifference > 5) {
                    $receiver = User::find($request->userId);

                    $notification = Notification::create([
                        'type' => null,
                        'ride_id' => $request->ride_id,
                        'posted_by' => $user->id,
                        'receiver_id' => $receiver->id,
                        'message' =>  'New message received from ' . $user->first_name,
                        'status' => 'completed',
                        'notification_type' => 'chat'
                    ]);
                    if (isset($receiver->email_notification) && $receiver->email_notification == 1) {

                        // Generate and send email
                        $booking = Booking::where('ride_id', $request->ride_id)->where('user_id', $user->id)->first();
                        $isBooked = !is_null($booking);
                        $type = $user->id === $ride->added_by ? 'driver' : 'passenger';
                        if ($booking ) {

                            $data = [ 'isBooked' => $isBooked,'type' => $type,'message' => $request->input('message'), 'receiverFirstName' => $receiver->first_name, 'senderFirstName' => $user->first_name, 'senderLastName' => $user->last_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
                            Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
                        }
                        else {
                            $data = [
                                'isBooked' => false,
                                'type' => $type,
                                'message' => $request->input('message'),
                                'receiverFirstName' => $receiver->first_name,
                                'senderFirstName' => $user->first_name,
                                'senderLastName' => $user->last_name,
                                // 'seats' => $booking->seats,
                                // 'price' => $booking->fare,
                                'from' => $ride->rideDetail[0]->departure,
                                'to' => $ride->rideDetail[0]->destination,
                                'date' => $ride->rideDetail[0]->date,
                                'time' => $ride->rideDetail[0]->time
                            ];
                            Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
                        }
                    }
                }
            } else if (!isset($rideFirstMessage) && empty($rideFirstMessage)) {

                $receiver = User::find($request->userId);

                $notification = Notification::create([
                    'type' => null,
                    'ride_id' => $request->ride_id,
                    'posted_by' => $user->id,
                    'receiver_id' => $receiver->id,
                    'message' =>  'New message received from ' . $user->first_name,
                    'status' => 'completed',
                    'notification_type' => 'chat'
                ]);

                // Generate and send email
                if (isset($receiver->email_notification) && $receiver->email_notification == 1) {

                    $booking = Booking::where('ride_id', $request->ride_id)->where('user_id', $user->id)->first();
                    $type = $user->id === $ride->added_by ? 'driver' : 'passenger';
                    $isBooked = !is_null($booking);
                    if ($booking) {

                        $data = [ 'isBooked' => $isBooked,'type' => $type,'message' => $request->input('message'), 'receiverFirstName' => $receiver->first_name, 'senderFirstName' => $user->first_name, 'senderLastName' => $user->last_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
                        Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
                    }
                    else {
                        $data = [
                            'isBooked' => false,
                            'type' => $type,
                            'message' => $request->input('message'),
                            'receiverFirstName' => $receiver->first_name,
                            'senderFirstName' => $user->first_name,
                            'senderLastName' => $user->last_name,
                            // 'seats' => $booking->seats,
                            // 'price' => $booking->fare,
                            // 'from' => $booking->departure,
                            // 'to' => $booking->destination,
                            // 'date' => $booking->ride->date,
                            // 'time' => $booking->ride->time
                        ];
                        Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
                    }
                }
            } else {
                $receiver = User::find($request->userId);

                $notification = Notification::create([
                    'type' => null,
                    'ride_id' => $request->ride_id,
                    'posted_by' => $user->id,
                    'receiver_id' => $receiver->id,
                    'message' =>  'New message received from ' . $user->first_name,
                    'status' => 'completed',
                    'notification_type' => 'chat'
                ]);
                if (isset($receiver->email_notification) && $receiver->email_notification == 1) {

                    // Generate and send email
                    $booking = Booking::where('ride_id', $request->ride_id)->where('user_id', $user->id)->first();
                    $type = $user->id === $ride->added_by ? 'driver' : 'passenger';
                    $isBooked = !is_null($booking);
                    if ($booking) {

                        $data = [ 'isBooked' => $isBooked,'type' => $type,'message' => $request->input('message'), 'receiverFirstName' => $receiver->first_name, 'senderFirstName' => $user->first_name, 'senderLastName' => $user->last_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
                        Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));

                       
                    } else {
                        $data = [
                            'isBooked' => false,
                            'type' => $type,
                            'message' => $request->input('message'),
                            'receiverFirstName' => $receiver->first_name,
                            'senderFirstName' => $user->first_name,
                            'senderLastName' => $user->last_name,
                            // 'seats' => $booking->seats,
                            // 'price' => $booking->fare,
                            // 'from' => $booking->departure,
                            // 'to' => $booking->destination,
                            // 'date' => $booking->ride->date,
                            // 'time' => $booking->ride->time
                        ];
                        Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
                    }
                }
                
            }

            if (empty($rideFirstMessage)) {
                $message1 = Message::create([
                    'ride_id' => $ride->id,
                    'receiver' => $request->input('userId'),
                    'sender' => $user->id,
                    'message' => $request->input('message'),
                    'redirect' => '1',
                    'ride_detail_id' => $rideDetailId != "" ? $rideDetailId : NULL
                ]);
                $message1 = Message::whereId($message1->id)->with('user', 'rideDetail')->first();
                // Use the redirect message as the main message for first message to avoid duplicate
                $message = $message1;
            } else {
                // Only create regular message if it's not the first message
                $message = Message::create([
                    'ride_id' => $ride->id,
                    'receiver' => $request->input('userId'),
                    'sender' => $user->id,
                    'message' => $request->input('message'),
                    'ride_detail_id' => $rideDetailId != "" ? $rideDetailId : NULL
                ]);
            }

            $message_count = Message::where('sender', $user->id)->where('receiver', $request->input('userId'))->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])->count();
            
            if (isset($contact_count) && $message_count > 0) {

                $contactUserId = explode(',', $contact_count->contact_user_id);
                if (in_array($request->input('userId'), $contactUserId)) {
                    
                } else {
                    $contact_count->user_inbox_count = $contact_count->user_inbox_count + 1;
            
                    $contacted_by = $contact_count->contact_user_id;
                    
                    if (!empty($contacted_by)) {
                        $contacted_by_array = explode(',', $contacted_by);
                        if (!in_array($request->input('userId'), $contacted_by_array)) {
                            $contacted_by_array[] = $request->input('userId');
                        }
                    } else {
                        $contacted_by_array = [$request->input('userId')];
                    }
                
                    $contact_count->contact_user_id = implode(',', $contacted_by_array);
                
                    $contact_count->save();
                    }
            } elseif (isset($contact_count) && $message_count == 0) {
            } else {
                $message_count = new UserMessageCount();
                $message_count->user_inbox_count = 1;
                $message_count->user_id = $user->id;
                $message_count->contact_user_id = $request->input('userId');
                $message_count->save();
            }

            // Message is already created above (either redirect message for first message, or regular message for subsequent messages)
            // Only create the message here if it wasn't created above
            if (!isset($message)) {
                $message = Message::create([
                    'ride_id' => $ride->id,
                    'receiver' => $request->input('userId'),
                    'sender' => $user->id,
                    'message' => $request->input('message'),
                    'ride_detail_id' => $rideDetailId != "" ? $rideDetailId : NULL
                ]);
            }

            // Assuming $user and $fcmToken are defined
            
            $receiver = User::find($request->userId);
            $fcmService = new FCMService();
            $fcm_tokens = FCMToken::where('user_id', $receiver->id)->get();
            $body = 'New message received from ' . $user->first_name;

            $fcmToken = $receiver->mobile_fcm_token;
            // Build extra payload per requested schema
            $extraData = [
                'notification_type' => 'chat received',
                'other_user_id' => (string) $user->id,
                'ride_id' => (string) $ride->id,
                'type' => 'new',
                'sender_id' => (string) $user->id,
                'receiver_id' => (string) $receiver->id,
                'message_id' => (string) $message->id,
                'message_preview' => (string) ($request->input('message') ?? ''),
                'created_at' => optional($message->created_at)->toISOString(),
            ];

            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body, "chat", $receiver->id, '', $extraData);
            }

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body, "chat", $receiver->id, '', $extraData);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }

            // Make sure message has user relationship loaded before broadcasting
            if (!$message->relationLoaded('user')) {
                $message->load('user');
            }

            // Broadcast the event synchronously (not queued) for immediate real-time updates
            try {
                broadcast(new MessageSentEvent($ride, $user, $message))->toOthers();
                Log::info('Message broadcasted', [
                    'message_id' => $message->id,
                    'sender' => $message->sender,
                    'receiver' => $message->receiver,
                    'ride_id' => $message->ride_id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to broadcast message: ' . $e->getMessage());
            }

            $message = Message::whereId($message->id)->with('user')->first();

            return ['status' => 'Message Sent!', 'message1' => $message1 ?? null, 'message' => $message];
        }
        return ['status' => $message->message_limit_exceeded_message ?? "Message limit exceeded"];
    }

}
