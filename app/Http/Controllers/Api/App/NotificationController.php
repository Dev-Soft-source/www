<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\ChatsPageSettingDetail;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Message;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\NotificationsPageSettingDetail;
use App\Models\NotificationsPageSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;

class NotificationController extends Controller
{
    use StatusResponser;

    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $notifications = Notification::where('is_delete', '0');

        $bookingType = $paymentMethod = "";
        if (isset($request->booking_type) && $request->booking_type != "") {
            $bookingType = $request->booking_type;
        }

        if (isset($request->payment_method) && $request->payment_method != "") {
            $paymentMethod = $request->payment_method;
        }

        if ($bookingType == "" && $paymentMethod == "") {
            $notifications = $notifications->where(function ($query) use ($user_id) {
                $query->where(function ($query) use ($user_id) {
                    $query->where('type', '1')
                        ->whereHas('ride', function ($query) use ($user_id) {
                            $query->where('added_by', $user_id);
                        });
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })->orWhere(function ($query) use ($user_id) {
                    $query->whereNull('type')
                        ->where('receiver_id', $user_id);
                });
            });
        } else {
            $notifications = $notifications->where(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                $query->where(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                    $query->where('type', '1')
                        ->whereHas('ride', function ($query) use ($user_id, $bookingType, $paymentMethod) {
                            $query->where('added_by', $user_id);
                            if ($bookingType != "") {
                                $query->where('booking_method', $bookingType);
                            }
                            if ($paymentMethod != "") {
                                $query->where('payment_method', $paymentMethod);
                            }
                        });
                })->orWhere(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id, $bookingType, $paymentMethod) {
                            $query->where('user_id', $user_id);
                            if ($bookingType != "") {
                                $query->whereHas('ride', function ($q) use ($bookingType) {
                                    $q->where('booking_method', $bookingType);
                                });
                            }
                            if ($paymentMethod != "") {
                                $query->whereHas('ride', function ($q) use ($paymentMethod) {
                                    $q->where('payment_method', $paymentMethod);
                                });
                            }
                        });
                });
            });
        }



        $notifications = $notifications->with(['from' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }])
            ->orderBy('id', 'desc')
            ->get();

        if ($request->lang_id && $request->lang_id != 0) {
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        foreach ($notifications as $notification) {
            if ($notification->from && $notification->from->gender) {
                if ($notification->from->gender === 'male') {
                    $notification->from->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($notification->from->gender === 'female') {
                    $notification->from->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($notification->from->gender === 'prefer not to say') {
                    $notification->from->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }
        }

        $data = ['notifications' => $notifications];
        return $this->successResponse($data, 'Get notifications successfully');
    }


    public function notifications(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;


        $notificationsPageSetting = NotificationsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $chatPageSetting = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $notificationRows = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
            $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                $query->where('added_by', $user_id);
            })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
                });
        })
            ->with([
                'from' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image')->withTrashed();
                },
                'booking' => function ($query) {
                    $query->select('id', 'from_stop_id', 'to_stop_id');
                },
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($notification) {
                $arr = $notification->toArray();
                if (array_key_exists('added_on', $arr)) {
                    $arr['created_at'] = $arr['added_on'];
                    unset($arr['added_on']);
                }
                if (array_key_exists('from', $arr)) {
                    $arr['sender'] = $arr['from'];
                    unset($arr['from']);
                }
                $fromStopId = $arr['from_stop_id'] ?? $notification->booking?->from_stop_id;
                $toStopId = $arr['to_stop_id'] ?? $notification->booking?->to_stop_id;
                $arr['from_stop_id'] = $fromStopId !== null && $fromStopId !== '' ? (int) $fromStopId : null;
                $arr['to_stop_id'] = $toStopId !== null && $toStopId !== '' ? (int) $toStopId : null;
                unset($arr['booking']);
                $arr['kind'] = 'notification';

                return $arr;
            })
            ->values();

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
            $messageArray['kind'] = 'chat';
            $messageArray['category'] = null;

            return $messageArray;
        })
            ->filter()
            ->values();

        $inboxFull = $notificationRows->concat($chats)
            ->sortByDesc(function ($item) {
                return $item['created_at'] ?? '';
            })
            ->values();

        $perPage = (int) $request->input('per_page', 20);
        $perPage = $perPage > 0 ? min($perPage, 100) : 20;
        $page = max((int) $request->input('page', 1), 1);

        $inbox = new LengthAwarePaginator(
            $inboxFull->forPage($page, $perPage)->values(),
            $inboxFull->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('notifications', compact(
            'inbox',
            'inboxFull',
            'chatPageSetting',
            'notificationsPageSetting',
            'user_id'
        ));
    }

    

    public function readNotification(Request $request)
    {
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $notification = Notification::where('is_delete', '0')->whereId($request->id)->first();
        if ($notification) {
            $notification->update(['is_read' => '1']);
        }

        $data = ['notification' => $notification];
        return $this->successResponse($data, 'Get notification successfully');
    }

    public function addToken(Request $request)
    {
        $user_id = Auth::guard('sanctum')->user()->id;

        // Validate the form data
        $request->validate([
            'token' => 'required',
        ]);

        $user = User::whereId($user_id)->update([
            'mobile_fcm_token' => $request->token,
        ]);

        $notifications = Notification::where('is_delete', '0')->where('is_read', '0');

        $notifications->where(function ($query) use ($user_id) {
            $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                $query->where('added_by', $user_id);
            })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
                });
        });

        $notifications = $notifications->orderBy('id', 'desc')->count();

        $data = ['notificationCount' => $notifications];

        return $this->successResponse($data, 'FCM token updated');
    }

    public function removeToken(Request $request)
    {
        $user_id = Auth::guard('sanctum')->user()->id;

        $user = User::whereId($user_id)->update([
            'mobile_fcm_token' => null,
        ]);

        return $this->successResponse('', 'FCM token removed');
    }

    public function deleteNotification(Request $request)
    {
        $id = $request->id;

        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        // Mark all unread notifications as read for this user
        Notification::where('is_delete', '0')
            ->where('is_read', '0')
            ->where(function ($query) use ($user_id) {
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
            ->orWhere(function ($query) use ($user_id) {
                $query->where('is_delete', '0')
                    ->where('is_read', '0')
                    ->where('type', '2')
                    ->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
            })
            ->orWhere(function ($query) use ($user_id) {
                $query->where('is_delete', '0')
                    ->where('is_read', '0')
                    ->where('type', null)
                    ->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
            })
            ->update(['is_read' => '1']);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    public function deleteAppNotification(Request $request)
    {
        $user_id = Auth::guard('sanctum')->user()->id;

        $notification = Notification::findOrFail($request->notificationId);
        $notification->delete();

        return $this->successResponse('', 'FCM token removed');
    }
}
