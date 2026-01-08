<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\ChatsPageSettingDetail;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PostRidePageSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $notifications = null;
        $notifications = Notification::where('is_delete', '0')->where('is_read', '0');

        $bookingType = $paymentMethod = "";
        if(isset($request->booking_type) && $request->booking_type != ""){
            $bookingType = $request->booking_type;
        }

        if(isset($request->payment_method) && $request->payment_method != ""){
            $paymentMethod = $request->payment_method;
        }

        if($bookingType == "" && $paymentMethod == ""){
            $notifications = $notifications->where(function ($query) use ($user_id) {
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
                $query->where('type', null)
                ->whereHas('receiver', function ($query) use ($user_id) {
                    $query->where('id', $user_id);
                });
            });
        }else{
            $notifications = $notifications->where(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                      ->whereHas('ride', function ($query) use ($user_id, $bookingType, $paymentMethod) {
                          $query->where('added_by', $user_id);
                          if($bookingType != ""){
                            $query->where('booking_method', $bookingType);
                          }
                          if($paymentMethod != ""){
                            $query->where('payment_method', $paymentMethod);
                          }
                      });
            })
            ->orWhere(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                // Ratings where type is 2 and booking_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user_id, $bookingType, $paymentMethod) {
                          $query->where('user_id', $user_id);
                          if($bookingType != ""){
                            $query->whereHas('ride', function($q) use($bookingType){
                                $q->where('booking_method', $bookingType);
                            });
                          }
                          if($paymentMethod != ""){
                            $query->whereHas('ride', function($q) use($paymentMethod){
                                $q->where('payment_method', $paymentMethod);
                            });
                          }
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
            if ($notification->from->gender) {
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
        $notifications = Notification::where('is_delete', '0');
        $bookingType = $request->input('booking_type', '');
        $paymentMethod = $request->input('payment_method', '');

        if ($bookingType === "" && $paymentMethod === "") {
            $notifications->where(function ($query) use ($user_id) {
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
            });
        } else {
            $notifications->where(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id, $bookingType, $paymentMethod) {
                    $query->where('added_by', $user_id);
                    if ($bookingType != "") {
                        $query->where('booking_method', $bookingType);
                    }
                    if ($paymentMethod != "") {
                        $query->where('payment_method', $paymentMethod);
                    }
                })
                ->orWhere(function ($query) use ($user_id, $bookingType, $paymentMethod) {
                    $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id, $bookingType, $paymentMethod) {
                        $query->where('user_id', $user_id);
                        if ($bookingType != "") {
                            $query->whereHas('ride', function($q) use($bookingType) {
                                $q->where('booking_method', $bookingType);
                            });
                        }
                        if ($paymentMethod != "") {
                            $query->whereHas('ride', function($q) use($paymentMethod) {
                                $q->where('payment_method', $paymentMethod);
                            });
                        }
                    });
                });
            });
        }

        $notifications = $notifications->with(['from' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image')->withTrashed();
        }])->orderBy('id', 'desc')->get();

        $selectedLanguage = Language::where('is_default', 1)->first();
        $languages = Language::all();
        $bookingOptions = PostRidePageSettingDetail::select('booking_option1', 'booking_option2')->first();
        $notificationPage = ChatsPageSettingDetail::select('notification_delete_text')->first();
        $successMessage = SuccessMessagesSettingDetail::select('cancel_button','delete_button')->first();
        $bookingOptions->booking_option1 = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option1)
            ->whereLanguageId($selectedLanguage->id)
            ->first();
        $bookingOptions->booking_option2 = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option2)
            ->whereLanguageId($selectedLanguage->id)
            ->first();

        $paymentMethodOptions = FindRidePageSettingDetail::select('payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'payment_methods_option4')->first();
        $paymentMethodOptions->payment_methods_option2 = FeaturesSettingDetail::whereFeaturesSettingId($paymentMethodOptions->payment_methods_option2)
            ->whereLanguageId($selectedLanguage->id)
            ->first();
        $paymentMethodOptions->payment_methods_option3 = FeaturesSettingDetail::whereFeaturesSettingId($paymentMethodOptions->payment_methods_option3)
            ->whereLanguageId($selectedLanguage->id)
            ->first();
        $paymentMethodOptions->payment_methods_option4 = FeaturesSettingDetail::whereFeaturesSettingId($paymentMethodOptions->payment_methods_option4)
            ->whereLanguageId($selectedLanguage->id)
            ->first();

        return view('notifications', compact('successMessage','notificationPage' ,'notifications', 'bookingOptions', 'paymentMethodOptions', 'selectedLanguage', 'languages'));
    }

    public function readNotification(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $notification = Notification::where('is_delete', '0')->whereId($request->id)->first();
        if ($notification) {
            $notification->update([
                'is_read' => '1',
            ]);
        }

        $data = ['notification' => $notification];
        return $this->successResponse($data, 'Get notification successfully');
    }

    public function addToken(Request $request){
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

    public function removeToken(Request $request){
        $user_id = Auth::guard('sanctum')->user()->id;

        $user = User::whereId($user_id)->update([
            'mobile_fcm_token' => null,
        ]);

        return $this->successResponse('', 'FCM token removed');
    }
    
    public function deleteNotification(Request $request){
        $id = $request->id;

        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request){
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

    public function deleteAppNotification(Request $request){
        $user_id = Auth::guard('sanctum')->user()->id;

        $notification = Notification::findOrFail($request->notificationId);
        $notification->delete();

        return $this->successResponse('', 'FCM token removed');
    }
}
