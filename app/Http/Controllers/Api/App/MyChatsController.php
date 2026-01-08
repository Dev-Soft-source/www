<?php

namespace App\Http\Controllers\Api\App;

use App\Events\MessageSentEvent;
use App\Http\Controllers\Controller;
use App\Mail\ReceiveChatMessageMail;
use App\Models\ChatsPageSettingDetail;
use App\Models\Message;
use App\Models\Notification;
use App\Models\FCMToken;
use App\Models\Ride;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\UserMessageCount;
use App\Models\Language;
use App\Models\Booking;
use App\Models\SuccessMessagesSettingDetail;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MyChatsController extends Controller
{
    use StatusResponser;

    public function index(){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $messages = Message::where('status', 'new')
            ->where(function ($query) use($user_id){
                $query->where('sender', $user_id)->orWhere('receiver', $user_id);
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($message) use ($user_id) {
                return $message->sender === $user_id ? $message->receiver : $message->sender;
            });

        $messages = $messages->map(function ($groupedMessages) use ($user_id) {
            $latestMessage = $groupedMessages->first()
                ->load(['user' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
                    $query->withTrashed(); // Include soft-deleted users
                }, 'receiver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
                    $query->withTrashed(); // Include soft-deleted users
                }]);
                $deletedBy = $latestMessage->deleted_by ? explode(',', $latestMessage->deleted_by) : [];
                if (in_array($user_id, $deletedBy)) {
                    return null; // Skip this message group
                }

            // Count unread messages (is_read = 0)
            $unreadCount = $groupedMessages->where('receiver', $user_id)
                ->where('is_read', 0)
                ->count();

                $messageArray = $latestMessage->toArray();
                $messageArray['sender'] = $messageArray['user'];
                unset($messageArray['user']);

                // Append unread count
                $messageArray['unread_count'] = $unreadCount;

                return $messageArray;
        })
        ->filter()
        ->values();


        
        $languages = Language::orderBy('is_default', 'desc')->get();
            
        $data = ['chats' => $messages, 'languages' => $languages];
        return $this->successResponse($data, 'Get chats successfully');
    }

    public function oldChats(){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $messages = Message::where('status', 'old')
            ->where(function ($query) use($user_id){
                $query->where('sender', $user_id)->orWhere('receiver', $user_id);
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($message) use ($user_id) {
                return $message->sender === $user_id ? $message->receiver : $message->sender;
            });

        $messages = $messages->map(function ($groupedMessages) {
            return $groupedMessages->first()
                ->load(['user' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
                    $query->withTrashed(); // Include soft-deleted users
                }, 'receiver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
                    $query->withTrashed(); // Include soft-deleted users
                }]);
        })
        
        ->map(function ($message) {
            // Rename 'user' to 'sender' in the response
            $messageArray = $message->toArray();
            $messageArray['sender'] = $messageArray['user'];
            unset($messageArray['user']);
            return $messageArray;
        })
        ->values();

        $data = ['chats' => $messages];
        return $this->successResponse($data, 'Get chats successfully');
    }

    public function chatDetail(Request $request){
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messageSetting = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('url_not_allowed_message', 'email_not_allowed_message', 'phone_number_not_allowed_message', 'popup_close_btn_text')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messageSetting = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('url_not_allowed_message', 'email_not_allowed_message', 'phone_number_not_allowed_message', 'popup_close_btn_text')->first();
            }
        }

        $userId = $request->user_id;
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $messages = Message::query();
        if($request->type == "old"){
            $messages = $messages->where('status', 'old');    
        }else{
            $messages = $messages->where('status', 'new');
        }

        if ($request->ride_id) {
            $messages = $messages->where('ride_id', $request->ride_id);
        }

        $messages = $messages->with(['user' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'profile_image', 'online', 'gender'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }, 'receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
            $query->withTrashed(); // Include soft-deleted users
        }, 'rideDetail' ])
        ->where(function($query) use ($user_id, $userId) {
            $query->where(function($q) use ($user_id, $userId) {
                $q->where('sender', $user_id)->where('receiver', $userId);
            })->orWhere(function($q) use ($user_id, $userId) {
                $q->where('sender', $userId)->where('receiver', $user_id);
            });
        })
        ->get();

        foreach ($messages as $message) {
            $message->where('receiver', $user_id)->update([
                'is_read' => '1',
            ]);
        }

        $messages = $messages->map(function ($message) {
            $messageArray = $message->toArray();
            $messageArray['sender'] = $messageArray['user'];
            unset($messageArray['user']);
            return $messageArray;
        });

        $user = User::whereId($userId)->select('id', 'first_name', 'last_name', 'profile_image', 'online','gender')->first();
        $data = ['user' => $user, 'messages' => $messages, 'messageSetting' => $messageSetting, 'chatsPage' => $chatsPage];
        return $this->successResponse($data, 'Get chats successfully');
    }

    public function store(Request $request){
        $user = Auth::guard('sanctum')->user();

        // Validate the form data
        $request->validate([
            'ride_id' => 'nullable',
            'receiver_id' => 'required',
            'message' => 'required',
        ]);


        $messages = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('message_limit_exceeded_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('message_limit_exceeded_message')->first();
            }
        }

        $contact_limit = SiteSetting::pluck('user_per_day_limit')->first();

        $contact_count = UserMessageCount::where('user_id', $user->id)
            ->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])
            ->first();

        if (!is_null($contact_count) && $contact_count->user_inbox_count > $contact_limit) {
            return $this->apiErrorResponse(strip_tags($message->message_limit_exceeded_message ?? "Message limit exceeded"), 200);
        }

        // If ride_id is present in the request, fetch the ride
        $ride = null;
        $rideDetailId = "";
        if ($request->has('ride_id') && !empty($request->ride_id)) {
            $ride = Ride::whereId($request->ride_id)->with(['rideDetail' => function($q){
                $q->where('default_ride','1');
            }])->first();

            if(isset($ride->rideDetail[0]) && !empty($ride->rideDetail[0])){
                $rideDetailId = $ride->rideDetail[0]->id;
            }
        }

        

        // Check the last message between the sender and receiver
        $lastMessage = Message::where(function ($query) use ($user, $request) {
            $query->where('sender', $user->id)
                  ->where('receiver', $request->receiver_id);
        })->orWhere(function ($query) use ($user, $request) {
            $query->where('sender', $request->receiver_id)
                  ->where('receiver', $user->id);
        })->latest()->first();

        
        // Check the ride first message
        $rideFirstMessage = Message::where(function ($query) use ($user, $request) {
            $query->where('sender', $user->id)
                  ->where('receiver', $request->receiver_id);
        })->orWhere(function ($query) use ($user, $request) {
            $query->where('sender', $request->receiver_id)
                  ->where('receiver', $user->id);
        })->where('ride_id', $request->ride_id)->first();

        if (!isset($rideFirstMessage) && empty($rideFirstMessage)) {
            if ($request->ride_id !== '0') {
                $msg = Message::create([
                    'ride_id' => $request->ride_id,
                    'receiver' => $request->receiver_id,
                    'sender' => $user->id,
                    'message' => $request->message,
                    'redirect' => '1',
                    'ride_detail_id' => $rideDetailId != "" ? $rideDetailId : NULL,
                ]);
            }
        }
        
        if ($lastMessage) {
            $lastMessageTime = $lastMessage->created_at;
            $timeDifference = now()->diffInMinutes($lastMessageTime);
    
            if ($timeDifference > 5) {
                $receiver = User::find($request->receiver_id);
                
                $notification = Notification::create([
                    'type' => null,
                    'ride_id' => $request->ride_id == 0 ? null : $request->ride_id,
                    'posted_by' => $user->id,
                    'receiver_id' => $receiver->id,
                    'message' =>  'New message received from ' . $user->first_name,
                    'status' => 'completed',
                    'notification_type' => 'chat'
                ]);

                // Generate and send email

                $booking = Booking::where('ride_id', $request->ride_id)->first();
                if(isset($booking) && !empty($booking)){
                    $data = ['receiverFirstName' => $receiver->first_name, 'senderFirstName' => $user->first_name, 'senderLastName' => $user->last_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
                
                    Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
                }
                
            }
        }else if(!isset($rideFirstMessage) && empty($rideFirstMessage)){

            $receiver = User::find($request->receiver_id);
                
            $notification = Notification::create([
                'type' => null,
                'ride_id' => $request->ride_id == 0 ? null : $request->ride_id,
                'posted_by' => $user->id,
                'receiver_id' => $receiver->id,
                'message' =>  'New message received from ' . $user->first_name,
                'status' => 'completed',
                'notification_type' => 'chat'
            ]);


            // Generate and send email

            $booking = Booking::where('ride_id', $request->ride_id)->first();
            if(isset($booking) && !empty($booking)){
                $data = ['receiverFirstName' => $receiver->first_name, 'senderFirstName' => $user->first_name, 'senderLastName' => $user->last_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
                Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));    
            }
        } else {
            $receiver = User::find($request->receiver_id);
                
            $notification = Notification::create([
                'type' => null,
                'ride_id' => $request->ride_id == 0 ? null : $request->ride_id,
                'posted_by' => $user->id,
                'receiver_id' => $receiver->id,
                'message' =>  'New message received from ' . $user->first_name,
                'status' => 'completed',
                'notification_type' => 'chat'
            ]);


            // Generate and send email

            $booking = Booking::where('ride_id', $request->ride_id)->first();
            $data = ['receiverFirstName' => $receiver->first_name, 'senderFirstName' => $user->first_name, 'senderLastName' => $user->last_name, 'seats' => $booking->seats, 'price' => $booking->fare, 'from' => $booking->departure, 'to' => $booking->destination, 'date' => $booking->ride->date, 'time' => $booking->ride->time];
            Mail::to($receiver->email)->queue(new ReceiveChatMessageMail($data));
        }


        $message_count = Message::where('sender', $user->id)->where('receiver', $request->input('userId'))->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])->count();

        if (isset($contact_count) && $message_count > 0) {

            $contactUserId = explode(',', $contact_count->contact_user_id);
            if (in_array($request->input('receiver_id'), $contactUserId)) {
                
            } else {
                $contact_count->user_inbox_count = $contact_count->user_inbox_count + 1;
        
                $contacted_by = $contact_count->contact_user_id;
                
                if (!empty($contacted_by)) {
                    $contacted_by_array = explode(',', $contacted_by);
                    if (!in_array($request->input('receiver_id'), $contacted_by_array)) {
                        $contacted_by_array[] = $request->input('receiver_id');
                    }
                } else {
                    $contacted_by_array = [$request->input('receiver_id')];
                }
            
                $contact_count->contact_user_id = implode(',', $contacted_by_array);
            
                $contact_count->save();
                }
        } elseif (isset($contact_count) && $message_count == 0) {
        } else {
            $message_count = new UserMessageCount();
            $message_count->user_inbox_count = 1;
            $message_count->user_id = $user->id;
            $message_count->contact_user_id = $request->input('receiver_id');
            $message_count->save();
        }

        $msg = Message::create([
            'ride_id' => $request->ride_id == 0 ? $lastMessage->ride_id : $request->ride_id,
            'receiver' => $request->receiver_id,
            'sender' => $user->id,
            'message' => $request->message,
            'ride_detail_id' => $rideDetailId != "" ? $rideDetailId : $lastMessage->ride_detail_id,
        ]);



        // Assuming $user and $fcmToken are defined

        $receiver = User::find($request->receiver_id);
        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $receiver->id)->get();
        $body = 'New message received from ' . $user->first_name;
        $title = 'New Message';

        // Extra data for deep linking to the specific chat
        $extraData = [
            'notification_type' => 'chat',
            'ride_id' => $request->ride_id ?? '',
            'sender_id' => $user->id,
            'other_user_id' => $user->id,
        ];

        $fcmToken = $receiver->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body, 'chat', $msg->id, $title, $extraData);
        }

        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body, 'chat', $msg->id, $title, $extraData);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
            }
        }


        $message = Message::where('status', 'new')->whereId($msg->id)->with(['user' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'profile_image', 'online','gender'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }, 'receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
            $query->withTrashed(); // Include soft-deleted users
        }])
        ->first();

        if ($message) {
            $messageArray = $message->toArray();
            $messageArray['sender'] = $messageArray['user'];
            unset($messageArray['user']);
            
            // Broadcast the message to Pusher WebSocket
            broadcast(new MessageSentEvent($ride, $user, $message))->toOthers();
        }

        return $this->successResponse($messageArray, 'Message Sent!');
    }

    public function chatsIndex(Request $request)
    {
        $chatsPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            // Retrieve the chatsPageSettingDetail associated with the selected language
            $chatsPage = ChatsPageSettingDetail::where('language_id', $request->lang_id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('url_not_allowed_message', 'email_not_allowed_message', 'phone_number_not_allowed_message', 'popup_close_btn_text')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('url_not_allowed_message', 'email_not_allowed_message', 'phone_number_not_allowed_message', 'popup_close_btn_text')->first();
            }
        }

        $data = ['chatsPage' => $chatsPage, 'messages' => $messages];
        return $this->successResponse($data, 'Chats page get successfully');
    }

    public function deleteChat(Request $request)
    {

        
        $user = Auth::guard('sanctum')->user();
        $currentUserId = $user->id;

        $messages = Message::where('receiver', $request->receiver['id'])
            ->where('sender', $request->sender['id'])
            ->where('status','new')
            ->get();
        
            foreach ($messages as $message) {
                $deletedBy = $message->deleted_by;
                $deletedByArray = $deletedBy ? explode(',', $deletedBy) : [];
            
                if (!in_array($currentUserId, $deletedByArray)) {
                    $deletedByArray[] = $currentUserId;
                    $message->deleted_by = implode(',', $deletedByArray); // save as comma-separated string
                    $message->save();
                }
            }
            
        
            return $this->successResponse('Chats deleted successfully');
    
    }
}
