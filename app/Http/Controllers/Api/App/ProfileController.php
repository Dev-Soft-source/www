<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\GovernmentIssuedIdUploadMail;
use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\State;
use App\Models\User;
use App\Models\Language;
use App\Models\EditProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Traits\StatusResponser;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    use StatusResponser;

    public function index(){
        $user = Auth::guard('sanctum')->user();
        $data = ['user' => $user];
        return $this->successResponse($data, 'Get user successfully');
    }

    public function profilePage(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
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
            $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
            $query->withTrashed(); // Include soft-deleted users
        }])
        ->orderBy('id', 'desc')
        ->take($request->reviews_limit)
        ->get();

        if ($request->lang_id && $request->lang_id != 0) {
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        foreach ($ratings as $rating) {
            $reply = $rating->replies()->first();
            $rating->replies = $reply ? $reply : null;
            if ($rating->from->gender) {
                if ($rating->from->gender === 'male') {
                    $rating->from->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($rating->from->gender === 'female') {
                    $rating->from->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($rating->from->gender === 'prefer not to say') {
                    $rating->from->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }
        }

        $total_reviews = Rating::where(function ($query) use ($user_id) {
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
        })->count();

        $passenger_driven = Ride::where('status', '!=', 2)->where('added_by', $user_id)
            ->where(function ($query) {
                $query->whereDate('rides.date', '<', now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->whereDate('rides.date', '=', now()->toDateString())
                            ->whereTime('rides.time', '<=', now()->toTimeString());
                    });
            })
            ->get()
            ->flatMap(function ($ride) {
                return $ride->bookings()->pluck('seats');
            })
            ->sum();

        $rides_taken = Ride::where('status', '!=', 2)->where('added_by', $user_id)
            ->where(function ($query) {
                $query->whereDate('rides.date', '<', now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->whereDate('rides.date', '=', now()->toDateString())
                            ->whereTime('rides.time', '<=', now()->toTimeString());
                    });
            })
            ->count();

        // Calculate age
        if ($user->dob) {
            $dob = Carbon::parse($user->dob);
            $user->age = $dob->diffInYears(Carbon::now());
        } else {
            $user->age = null; // Handle case where dob is not set
        }

        $editProfilePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $editProfilePage = EditProfilePageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        
        $data = ['user' => $user, 'passenger_driven' => $passenger_driven, 'rides_taken' => $rides_taken, 'km_shared' => '0', 'total_reviews' => $total_reviews, 'ratings' => $ratings, 'editProfilePage' => $editProfilePage];
        return $this->successResponse($data, 'Get user successfully');
    }

    public function edit(Request $request){
        $user = Auth::guard('sanctum')->user();
        $country = Country::where('id', $user->country)->first();
        $state = State::where('id', $user->state)->first();
        $city = City::where('id', $user->city)->first();

        $editProfilePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $editProfilePage = EditProfilePageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'string' => trans('validation.string'),
            'max.string' => trans('validation.max.string'),
            'date' => trans('validation.date'),
            'file' => trans('validation.file'),
            'mimes' => trans('validation.mimes'),
            'max.file' => trans('validation.max.file'),
        ];

        $data = ['user' => $user, 'country' => $country, 'state' => $state, 'city' => $city, 'editProfilePage' => $editProfilePage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get data successfully');
    }

    public function update(Request $request){
        $user = Auth::guard('sanctum')->user();

        $customMessages = [
            'government_issued_id.max' => 'Can not upload image size greater than 10MB',
        ];
        $validated = $request->validate([
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

        // Map 'bio' to 'about' in the $validated array
        $validated['about'] = $validated['bio'];
        unset($validated['bio']); // Remove the 'bio' field

        User::whereId($request->id)->update($validated);

        $user = User::whereId($request->id)->first();

        if (isset($government_issued_id) && $request->hasFile('government_issued_id')) {
            $file = $request->file('government_issued_id');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/users_government_ids');
            $file->move($destination_path,$filename);

            //Original Image Upload
            $file = $request->file('government_issued_original_id');
            $filenameoriginal = $file->getClientOriginalName();
            $destination_path = public_path('/users_government_ids');
            $file->move($destination_path,$filenameoriginal);

            User::whereId($request->id)->update([
                'government_issued_id' => $filename,
                'government_issued_original_id' => $filenameoriginal,
                'profile_complete' => '1',
            ]);

            $country = Country::whereId($user->country)->first();
            $admin = Admin::first();

            
            $defaultLangId = Language::where('is_default', '1')->value('id');

            $data = ['username' => $admin->username,'first_name' => $user->first_name,'last_name' => $user->last_name,'email' => $user->email,'phone' => $user->phone,'country' => $country->name, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId];
            // Send upload email
            Mail::to($admin->admin_email)->queue(new GovernmentIssuedIdUploadMail($data));
        } 

        User::whereId($request->id)->update([
            'profile_complete' => '1',
        ]);
        
        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('profile_update_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('profile_update_message')->first();
            }
        }
        
        $data = ['user' => $user];
        return $this->successResponse($data, strip_tags($message->profile_update_message));
    }

    public function driverInfo(Request $request){
        $ride = Ride::select('id','added_by','make','model','vehicle_type','year','license_no','car_type','car_image')->whereId($request->ride_id)
            ->with(['driver' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image', 'about', 'created_at'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])->first();

        $ratings = null;

        $editProfilePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $editProfilePage = EditProfilePageSettingDetail::where('language_id', $request->lang_id)->first();
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        if ($ride) {
            $driver_id = $ride->driver->id;

            if ($ride->driver->gender) {
                if ($ride->driver->gender === 'male') {
                    $ride->driver->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($ride->driver->gender === 'female') {
                    $ride->driver->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($ride->driver->gender === 'prefer not to say') {
                    $ride->driver->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }
    
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
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->orderBy('id', 'desc')
            ->take($request->reviews_limit)
            ->get();

            foreach ($ratings as $rating) {
                $reply = $rating->replies()->first();
                $rating->replies = $reply ? $reply : null;
                
                if ($rating->from->gender) {
                    if ($rating->from->gender === 'male') {
                        $rating->from->gender_label = $genderLabel->male_option_label ?? null;
                    } elseif ($rating->from->gender === 'female') {
                        $rating->from->gender_label = $genderLabel->female_option_label ?? null;
                    } elseif ($rating->from->gender === 'prefer not to say') {
                        $rating->from->gender_label = $genderLabel->prefer_option_label ?? null;
                    }
                }
            }

            // Calculate passenger_driven
            $ride->driver->passenger_driven = Ride::where('added_by', $driver_id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->whereDate('completed_date', '<=', now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->whereDate('completed_date', '=', now()->toDateString())
                            ->whereTime('completed_time', '<=', now()->toTimeString());
                    });
            })
            ->get()
            ->flatMap(function ($ride) {
                return $ride->bookings()->pluck('seats');
            })
            ->sum();

            $ride->driver->rides_taken = Ride::where('status', '!=', 2)->where('added_by', $driver_id)
                ->where(function ($query) {
                    $query->whereDate('rides.date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('rides.date', '=', now()->toDateString())
                                ->whereTime('rides.time', '<=', now()->toTimeString());
                        });
                })
                ->count();

            $ride->driver->km_sared = 0;

            $total_reviews = Rating::where(function ($query) use ($driver_id) {
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
            })->count();
        }

        $data = ['ride' => $ride,'ratings' => $ratings,'total_reviews' => $total_reviews, 'editProfilePage' => $editProfilePage];
        return $this->successResponse($data, 'Get driver info page successfully');
    }

    public function passengerInfo(Request $request){
        $user = User::select('id', 'first_name', 'last_name', 'gender', 'dob', 'profile_image', 'about', 'created_at')->whereId($request->user_id)->first();
        $ratings = null;
        $total_reviews = null;
        
        $editProfilePage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $editProfilePage = EditProfilePageSettingDetail::where('language_id', $request->lang_id)->first();
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $editProfilePage = EditProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            }
        }

        if ($user) {
            // Calculate age
            if ($user->dob) {
                $dob = Carbon::parse($user->dob);
                $user->age = $dob->diffInYears(Carbon::now());
            } else {
                $user->age = null; // Handle case where dob is not set
            }

            if ($user->gender) {
                if ($user->gender === 'male') {
                    $user->gender_label = $genderLabel->male_option_label ?? null;
                } elseif ($user->gender === 'female') {
                    $user->gender_label = $genderLabel->female_option_label ?? null;
                } elseif ($user->gender === 'prefer not to say') {
                    $user->gender_label = $genderLabel->prefer_option_label ?? null;
                }
            }

            $user_id = $user->id;
    
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user_id) {
                          $query->where('user_id', $user_id);
                      })
                      ->where('status', 1);
    
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($user_id) {
                              $query->where('added_by', $user_id);
                          })
                          ->where('status', 1);
                });
            })
            ->with(['from' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'gender', 'profile_image'); // Specify the columns you want to select
                $query->withTrashed(); // Include soft-deleted users
            }])
            ->orderBy('id', 'desc')
            ->take($request->reviews_limit)
            ->get();

            foreach ($ratings as $rating) {
                $reply = $rating->replies()->first();
                $rating->replies = $reply ? $reply : null;
                
                if ($rating->from->gender) {
                    if ($rating->from->gender === 'male') {
                        $rating->from->gender_label = $genderLabel->male_option_label ?? null;
                    } elseif ($rating->from->gender === 'female') {
                        $rating->from->gender_label = $genderLabel->female_option_label ?? null;
                    } elseif ($rating->from->gender === 'prefer not to say') {
                        $rating->from->gender_label = $genderLabel->prefer_option_label ?? null;
                    }
                }
            }

            // Calculate passenger_driven
            $user->passenger_driven = Ride::where('added_by', $user_id)
                ->where('status', '!=', 2)
                ->where(function ($query) {
                    $query->whereDate('completed_date', '<=', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<=', now()->toTimeString());
                        });
                })
                ->get()
                ->flatMap(function ($ride) {
                    return $ride->bookings()->pluck('seats');
                })
                ->sum();

            $user->rides_taken = Ride::where('status', '!=', 2)->where('added_by', $user_id)
                ->where(function ($query) {
                    $query->whereDate('rides.date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('rides.date', '=', now()->toDateString())
                                ->whereTime('rides.time', '<=', now()->toTimeString());
                        });
                })
                ->count();

            $user->km_sared = 0;

            $total_reviews = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user_id) {
                          $query->where('user_id', $user_id);
                      })
                      ->where('status', 1);
    
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($user_id) {
                              $query->where('added_by', $user_id);
                          })
                          ->where('status', 1);
                });
            })->count();
        }

        $data = ['user' => $user,'total_reviews' => $total_reviews,'ratings' => $ratings, 'editProfilePage' => $editProfilePage];
        return $this->successResponse($data, 'Get passenger profile successfully');
    }
}