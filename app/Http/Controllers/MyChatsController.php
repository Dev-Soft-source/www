<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ChatsPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyChatsController extends Controller
{
    public function index($lang = null)
    {

        $chatsPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $successMessage = $this->successMessage;


        $user = auth()->user();
        if ($user->step === '1') {
            return redirect()->route('step1to5', ['lang' => $this->selectedLanguage->abbreviation]);
        } elseif ($user->step === '2') {
            return redirect()->route('step2to5', ['lang' => $this->selectedLanguage->abbreviation]);
        } elseif ($user->step === '3') {
            return redirect()->route('step3to5', ['lang' => $this->selectedLanguage->abbreviation]);
        } elseif ($user->step === '4') {
            return redirect()->route('step4to5', ['lang' => $this->selectedLanguage->abbreviation]);
        }
        $user_id = auth()->user()->id;

        $chats = Message::where(function ($query) use ($user_id) {
            $query->where('sender', $user_id)->orWhere('receiver', $user_id);
        })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($message) use ($user_id) {
                // Group by both user ID and ride ID to separate conversations properly
                $otherUserId = $message->sender == $user_id ? $message->receiver : $message->sender;
                return $otherUserId . '_' . ($message->ride_id ?? '0');
            });

        $chats = $chats->map(function ($groupedMessages) use ($user_id) {
            // Filter out messages that are deleted by this user
            $visibleMessages = $groupedMessages->filter(function ($message) use ($user_id) {
                $deletedBy = $message->deleted_by ? explode(',', $message->deleted_by) : [];
                return !in_array((string)$user_id, $deletedBy);
            });

            // If all messages are deleted, skip this group
            if ($visibleMessages->isEmpty()) {
                return null;
            }

            // Sort by created_at descending to get the latest message first
            $visibleMessages = $visibleMessages->sortByDesc('created_at');

            // Get the latest visible message
            $latestMessage = $visibleMessages->first()
                ->load(['user' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online', 'gender');
                    $query->withTrashed(); // Include soft-deleted users
                }, 'receiver' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_image', 'dob', 'online', 'gender');
                    $query->withTrashed(); // Include soft-deleted users
                }]);

            // Count unread messages (is_read = 0) from visible messages only
            $unreadCount = $visibleMessages->where('receiver', $user_id)
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
            ->values()
            ->sortByDesc(function ($chat) {
                return $chat['created_at'] ?? '';
            })
            ->values();

        return view('my_chats', [
            'successMessage' => $successMessage,
            'chats' => $chats,
            'user_id' => $user_id,
            'chatsPage' => $chatsPage
        ]);
    }


    public function oldChats($lang = null)
    {
        $chatsPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $user_id = auth()->user()->id;


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


        return view('old_chats', [
            'chats' => $chats,
            'user_id' => $user_id,
            'chatsPage' => $chatsPage
        ]);
    }


    public function deleteChat(Request $request)
    {
        // $request->validate([
        //     'ride_id' => 'required|integer',
        //     'receiver.id' => 'required|integer',
        //     'sender.id' => 'required|integer',
        // ]);

        $currentUserId = auth()->id();

        // Check if receiver and sender exist and have id
        $receiverId = isset($request->receiver['id']) ? $request->receiver['id'] : null;
        $senderId = isset($request->sender['id']) ? $request->sender['id'] : null;

        if (!$receiverId || !$senderId) {
            return redirect()->back()->with('error', 'Invalid chat data.');
        }

        $messages = Message::where('receiver', $receiverId)
            ->where('sender', $senderId)
            ->where('status', 'new')
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
