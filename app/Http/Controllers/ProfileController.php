<?php

namespace App\Http\Controllers;

use App\Mail\GovernmentIssuedIdUploadMail;
use App\Models\Admin;
use App\Models\ChatsPageSettingDetail;
use App\Models\City;
use App\Models\Country;
use App\Models\EditProfilePageSettingDetail;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\State;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $editProfilePage = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button','delete_button')->first();
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();


            if ($user->step === '1') {
                return redirect()->route('step1to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '2') {
                return redirect()->route('step2to5', ['lang' => $selectedLanguage->abbreviation]);
            } elseif ($user->step === '3') {
                return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
            }


            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user_id) {
                          $query->where('user_id', $user_id);
                      });
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($user_id) {
                              $query->where('added_by', $user_id);
                          });
                });
            })
            ->with(['from' => function ($query) {
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

            $notifications = Notification::where('is_delete', '0');
            $notifications = $notifications->where(function ($query) use ($user_id) {
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
            ->orderBy('id', 'desc')
            ->get();


            User::whereId($user_id)->update([
                'step' => '5'
            ]);

            $showWelcomePopup = session()->has('show_welcome_popup');
            if ($user->step === '5' && !$showWelcomePopup) {
                session(['show_welcome_popup' => true]);
                return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation])->with('message', "Your profile is all set. Welcome to ProximaRide!");
            }

            return view('profile',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'user' => $user,'editProfilePage' => $editProfilePage,'reviewSetting' => $reviewSetting,'ProfileSetting' => $ProfileSetting,'ProfilePage' => $ProfilePage,'ratings' => $ratings,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function profileInfo($lang = null, $id){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        $user = User::whereId($id)->first();
        $ratings = Rating::where(function ($query) use ($id) {
            // Ratings where type is 2 and user_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($id) {
                      $query->where('user_id', $id);
                  })
                  ->where('status', 1);

            // OR Ratings where type is 1 and ride_id belongs to the user
            $query->orWhere(function ($query) use ($id) {
                $query->where('type', '1')
                      ->whereHas('ride', function ($query) use ($id) {
                          $query->where('added_by', $id);
                      })
                      ->where('status', 1);
            });
        })
        ->with(['from' => function ($query) {
            $query->withTrashed(); // Include soft-deleted users
        }])
        ->orderBy('id', 'desc')
        ->get();

        $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($id) {
                      $query->where('added_by', $id);
                  });
        })
        ->orWhere(function ($query) use ($id) {
            // Ratings where type is 2 and booking_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($id) {
                      $query->where('user_id', $id);
                  });
        })
        ->orWhere(function ($query) use ($id) {
            // Ratings where type is null and receiver_id belongs to the user
            $query->where('type', null)
                  ->whereHas('receiver', function ($query) use ($id) {
                      $query->where('id', $id);
                  });
        })
        ->orderBy('id', 'desc')
        ->get();

        return view('profile_info',['user' => $user,'ratings' => $ratings,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function driverInfo($lang = null, $id){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        $ride = Ride::whereId($id)->first();
        $driver_id = $ride->driver->id;

        $ratings = Rating::where(function ($query) use ($driver_id) {
            // Ratings where type is 2 and user_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($driver_id) {
                      $query->where('user_id', $driver_id);
                  })
                  ->where('status', 1);

            // OR Ratings where type is 1 and ride_id belongs to the user
            $query->orWhere(function ($query) use ($driver_id) {
                $query->where('type', '1')
                      ->whereHas('ride', function ($query) use ($driver_id) {
                          $query->where('added_by', $driver_id);
                      })
                      ->where('status', 1);
            });
        })
        ->with(['from' => function ($query) {
            $query->withTrashed(); // Include soft-deleted users
        }])
        ->orderBy('id', 'desc')
        ->get();

        $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($driver_id) {
            // Ratings where type is 1 and ride_id belongs to the user
            $query->where('type', '1')
                  ->whereHas('ride', function ($query) use ($driver_id) {
                      $query->where('added_by', $driver_id);
                  });
        })
        ->orWhere(function ($query) use ($driver_id) {
            // Ratings where type is 2 and booking_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($driver_id) {
                      $query->where('user_id', $driver_id);
                  });
        })
        ->orWhere(function ($query) use ($driver_id) {
            // Ratings where type is null and receiver_id belongs to the user
            $query->where('type', null)
                  ->whereHas('receiver', function ($query) use ($driver_id) {
                      $query->where('id', $driver_id);
                  });
        })
        ->orderBy('id', 'desc')
        ->get();

        return view('driver_info',['ride' => $ride,'ratings' => $ratings,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }
    
    public function edit($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $editProfilePage = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
            }
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            $countries = Country::where('status', '1')->orderBy('name', 'asc')->get();
            $states = State::where('status', '1')->get();
            $cities = City::where('status', '1')->get();

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

            return view('edit_profile',['editProfilePage' => $editProfilePage,'reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'user' => $user,'countries' => $countries,'states' => $states,'cities' => $cities,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function update($id, Request $request){
        $customMessages = [
            'string' => 'The :attribute must be a string',
            'max' => 'The :attribute may not be greater than :max characters',
            'date' => 'The :attribute must be a valid date',
            'file' => 'Please select a valid file',
            'mimes' => 'The :attribute must be a file of type: jpeg, png',
            'uploaded' => 'The image is not uploaded yet',
            'government_issued_id.max' => 'Can not upload image size greater than 10MB',
        ];

        $filename = "";
        
        if (isset($request->government_issued_id) && $request->hasFile('government_issued_id')) {
            $file = $request->file('government_issued_id');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('users_government_ids');
            $file->move($destination_path,$filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        }

        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:25',
            'last_name' => 'required|string|max:25',
            'type' => 'nullable',
            'gender' => 'required',
            'dob' => 'required|date',
            'country' => 'required',
            'address' => 'nullable|string|max:100',
            'state' => 'required|string|max:25',
            'city' => 'required|string|max:25',
            'zipcode' => 'required|string|max:7',
            'government_issued_id' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240',
            'bio' => 'required|string',
        ], $customMessages);

        if ($validator->fails()) {
            // Check if there are validation errors for the 'uploaded' attribute
            $hasRequiredError = $validator->errors()->has('government_issued_id') && $validator->errors()->first('government_issued_id') === 'The image is not uploaded yet';
            // If there are other validation errors or the 'image' error is not present, return back with errors and the uploaded image path
            if (!$hasRequiredError || $validator->errors()->count() > 1) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('uploaded_image', $filename ?? null);
            }
        }
        $isAdmin=Auth::guard('admin')->user();
          User::whereId($id)->update([
              'first_name' => $request->first_name,
              'last_name' => $request->last_name,
              'type' => $request->type ?? '',
              'gender' => $request->gender,
              'dob' => $request->dob,
              'country' => $request->country,
              'address' => $request->address,
              'state' => $request->state,
              'city' => $request->city,
              'zipcode' => $request->zipcode,
              'government_issued_id' => $filename == "" ? NULL : $filename,
              'about' => $request->bio,
              'updated_by' => $isAdmin ? $isAdmin->id : $id,
              'email_notification' => isset($request->email_notification) ? 1 : 0,
              'sms_notification' => isset($request->sms_notification) ? 1 : 0,
              'profile_complete' => '1',
          ]);

        $user = User::whereId($id)->first();
        $country = Country::whereId($user->country)->first();
        $admin = Admin::first();

        
        $defaultLangId = Language::where('is_default', '1')->value('id');

        $data = ['username' => $admin->username,'first_name' => $user->first_name,'last_name' => $user->last_name,'email' => $user->email,'phone' => $user->phone,'country' => $country->name, 'lang_id' => $user->lang_id, 'defaultLangId' => $defaultLangId];
        // Send upload email
        Mail::to($admin->admin_email)->queue(new GovernmentIssuedIdUploadMail($data));

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('profile_update_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('profile_update_message')->first();
            }
        }
        
        return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->profile_update_message);
    }
}