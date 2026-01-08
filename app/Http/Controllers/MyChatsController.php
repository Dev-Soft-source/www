<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ChatsPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;

class MyChatsController extends Controller
{
    public function index($lang = null)
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
            $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_button','cancel_button')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $chatsPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_button','cancel_button')->first();
        }

        $user = auth()->user();
        if ($user->step === '1') {
            return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '2') {
            return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '3') {
            return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
        } elseif ($user->step === '4') {
            return redirect()->route('step5to5', ['lang' => $selectedLanguage->abbreviation]);
        }

        $notifications = null;
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

        $chats = Message::where('status', 'new')->where(function ($query) use($user_id){
            $query->where('sender', $user_id)->orWhere('receiver', $user_id);
        })->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($message) use ($user_id) {
                return $message->sender == $user_id ? $message->receiver : $message->sender;
            });

            $chats = $chats->map(function ($groupedMessages) use ($user_id) {
                $latestMessage = $groupedMessages->first()
                    ->load(['user' => function ($query) {
                        $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
                        $query->withTrashed(); // Include soft-deleted users
                    }, 'receiver' => function ($query) {
                        $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online','gender');
                        $query->withTrashed(); // Include soft-deleted users
                    }]);
                    $deletedBy = $latestMessage->deleted_by ? explode(',', $latestMessage->deleted_by) : [];
                    if (in_array((string)$user_id, $deletedBy)) {
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
        return view('my_chats', ['successMessage' => $successMessage,'chats' => $chats, 'user_id' => $user_id, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'chatsPage' => $chatsPage]);
    }

    public function oldChats($lang = null)
    {
        $languages = Language::all();

        $chatsPage = null;
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
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

        $chats = Message::with('rideDetail', 'ride:id,date,time')->where('status', 'new')->whereIn('sender', [$user_id])
            ->orWhereIn('receiver', [$user_id])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($user_id) {
                // Group by both user ID and ride ID
                return $message->sender === $user_id ? $message->receiver . '_' . $message->ride_id : $message->sender . '_' . $message->ride_id;
            })
            ->map(function ($groupedMessages) {
                // For each group, retrieve the latest message
                return $groupedMessages->sortByDesc('created_at')->first();
            });


        return view('old_chats', ['chats' => $chats, 'user_id' => $user_id, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'chatsPage' => $chatsPage]);
    }


    public function deleteChat(Request $request)
    {
        // $request->validate([
        //     'ride_id' => 'required|integer',
        //     'receiver.id' => 'required|integer',
        //     'sender.id' => 'required|integer',
        // ]);
    
        $currentUserId = auth()->id();

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
            
        
    
        return redirect()->back()->with('status', 'Chat deleted for you.');
    }
    
}
