<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LoginPageSettingDetail;
use App\Models\Rating;
use App\Models\Step1PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use StatusResponser;

    public function create(Request $request)
    {
        $loginPage = LoginPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $messages = $this->successMessage;

        $validationMessages = [
            'required' => trans('validation.required'),
            'string' => trans('validation.string'),
            'max' => trans('validation.max.string'),
            // Localized field names (for :attribute-style messages or client UI)
            'attributes' => [
                'email' => __('validation.attributes.email'),
                'password' => __('validation.attributes.password'),
            ],
            'email' => [
                'required' => trans('validation.custom.email.required'),
                'email' => trans('validation.custom.email.email'),
                'unique' => trans('validation.custom.email.unique'),
            ],
            'password' => [
                'required' => trans('validation.custom.password.required'),
            ],
        ];

        $data = ['loginPage' => $loginPage, 'validationMessages' => $validationMessages, 'messages' => $messages];
        return $this->successResponse($data, 'Login page get successfully');
    }

    public function show()
    {
        $data['user'] = Auth::guard('sanctum')->user();
        return $this->successResponse($data, 'Token is valid');
    }

    public function login(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email|string|max:255',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();

        $defaultLangId = 0;
        if(isset($request->lang_id)){
            $defaultLangId = $request->lang_id;
        }else{
            $defaultLangId = Language::where('is_default', '1')->value('id');
        }

        $message = $this->successMessage;
        $genderLabel = Step1PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        if ($user && $user->closed === '1') {
            return response()->json([
                'status' => 'Error',
                'message' => $message->account_closed_message
                    ?? "It looks like this account has been closed. We'd love to have you back! You can sign up for a new account using this email address anytime.",
                'errors' => null,
            ], 200);
        }

        
        if ($user && !$user->trashed() && $user->email_verified != 0 && auth()->attempt($credentials)) {

            $getNew = User::where('id', $user->id)->first();
            $getNew->lang_id = $defaultLangId;
            $getNew->save();
            if ($user->gender === 'male') {
                $user->gender_label = $genderLabel->male_option_label;
            } elseif ($user->gender === 'female') {
                $user->gender_label = $genderLabel->female_option_label;
            } elseif ($user->gender === 'prefer not to say') {
                $user->gender_label = $genderLabel->prefer_option_label;
            }

            if ($user->admin_deactive_account === '1') {
                $data = ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => $user->gender, 'gender_label' => $user->gender_label, 'profile_image' => $user->profile_image, 'profile_original_image' => $user->profile_original_image, 'email' => $user->email, 'id' => $user->id, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId, 'driver_liscense' => $user->driver_liscense];
                return array(
                    'status' => 'noShow',
                    'message' => $message->admin_block_account_message ?? 'Your account is suspended. Please contact us if you feel it should be reinstated',
                    'data' => $data,
                    'html' => '',
                );
            }

            $ipAddress = null;
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                        $ip = trim($ip); // just to be safe
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            $ipAddress = $ip;
                            break 2;
                        }
                    }
                }
            }
            $ipAddress = $ipAddress ?? 'UNKNOWN';

            $record = DB::table('user_details')->where('user_id', $user->id)->where('ip_address', $ipAddress)->first();
            if (!$record) {
                DB::table('user_details')->insert([
                    'ip_address' => $ipAddress,
                    'type' => 'web',
                    'user_id' => $user->id,
                    'created_at' => now()
                ]);
            }

            // Authentication passed
            $token = $user->createToken('ridesharing')->plainTextToken;

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($user) {
                return optional($rating->ride)->added_by === $user->id;
            });

            $driver_total_ratings = $filteredRatings->count();
            $totalAverage = $filteredRatings->avg('average_rating');
            $driver_average_rating = $totalAverage;

            $ratings = Rating::where('status', 1)->where('type', '2')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($user) {
                return optional($rating->booking)->user_id === $user->id;
            });

            $passenger_total_ratings = $filteredRatings->count();
            $totalAverage = $filteredRatings->avg('average_rating');
            $passenger_average_rating = $totalAverage;
            
            $ratings = Rating::where(function ($query) use ($user) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user) {
                          $query->where('user_id', $user->id);
                      });
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($user) {
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($user) {
                              $query->where('added_by', $user->id);
                          });
                });
            })->get();

            $user_total_ratings = $ratings->count();
            $totalAverage = $ratings->avg('average_rating');
            $user_average_rating = $totalAverage;
            $defaultLangId = Language::where('is_default', '1')->value('id');

            $languages = Language::orderBy('is_default', 'desc')->get();

            $data = ['token' => $token, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'gender' => $user->gender, 'gender_label' => $user->gender_label, 'profile_image' => $user->profile_image, 'profile_original_image' => $user->profile_original_image, 'email' => $user->email, 'student' => $user->student, 'student_card_exp_date' => $user->student_card_exp_date, 'driver' => $user->driver, 'step' => $user->step, 'id' => $user->id, 'driver_average_rating' => $driver_average_rating, 'passenger_average_rating' => $passenger_average_rating, 'user_average_rating' => $user_average_rating, 'driver_total_ratings' => $driver_total_ratings, 'passenger_total_ratings' => $passenger_total_ratings, 'user_total_ratings' => $user_total_ratings, 'langId' => isset($user->lang_id) ? $user->lang_id : $defaultLangId, 'languages' => $languages, 'driver_liscense' => $user->driver_liscense];
            return $this->successResponse($data, 'Login successfully!');
        } else {
            

            if ($user && $user->trashed()) {
                // User account is soft deleted
                return $this->apiErrorResponse('Account is not available anymore', 200);
            } elseif ($user && $user->email_verified == 0) {
                $data = ['verify_email' => true, 'email' => $user->email, 'url' => route('sendEmailVerify', ['email' => $user->email])];
                return $this->apiErrorResponse($message->verified_email_message ?? null, 200, $data);
            } elseif ($user) {
                return response()->json([
                    'status' => 'Error',
                    'message' => $message->no_password_match_message ?? 'The password you entered is incorrect.',
                    'errors' => null,
                ], 200);
            }

            // Authentication failed
            return response()->json([
                'status' => 'Error',
                'message' => $message->no_user_match_message,
                'errors' => null,
            ], 200);
        }
    }

    public function redirectToProvider(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'type_id' => 'required',
            'user_name' => 'required',
            'email' => 'required',
            'photourl' => 'required',
        ]);

        $message = null;


        $defaultLangId = 0;
        if(isset($request->lang_id)){
            $defaultLangId = $request->lang_id;
        }else{
            $defaultLangId = Language::where('is_default', '1')->value('id');
        }

        // Check if the user is already registered
        $existingUser = User::where('email', $request->email)->first();

        if ($request->lang_id && $request->lang_id != 0) {
            $genderLabel = Step1PageSettingDetail::where('language_id', $request->lang_id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('email_already_exist_message','general_error_message', 'account_closed_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $genderLabel = Step1PageSettingDetail::where('language_id', $selectedLanguage->id)->select('male_option_label', 'female_option_label', 'prefer_option_label')->first();
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('email_already_exist_message','general_error_message', 'account_closed_message')->first();
            }
        }

        if ($existingUser) {
            if ($existingUser->closed === '1') {
                return response()->json([
                    'status' => 'Error',
                    'message' => $message->account_closed_message
                        ?? "It looks like this account has been closed. We'd love to have you back! You can sign up for a new account using this email address anytime.",
                    'errors' => null,
                ], 200);
            }

            $existingUser->lang_id = $defaultLangId;
            $existingUser->email_verified = '1';

            if (empty($existingUser->provider)) {
                $existingUser->provider = $request->type;
            }

            if (empty($existingUser->provider_id)) {
                $existingUser->provider_id = $request->type_id;
            }

            if (empty($existingUser->profile_image) && !empty($request->photourl)) {
                $existingUser->profile_image = $request->photourl;
            }

            $existingUser->save();
            // Log in the existing user
            auth()->login($existingUser);

            $token = $existingUser->createToken('ridesharing')->plainTextToken;

            $ratings = Rating::where('status', 1)->where('type', '1')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($existingUser) {
                return optional($rating->ride)->added_by === $existingUser->id;
            });

            $driver_total_ratings = $filteredRatings->count();
            $totalAverage = $filteredRatings->avg('average_rating');
            $driver_average_rating = $totalAverage;

            $ratings = Rating::where('status', 1)->where('type', '2')->get();
            // Calculate average rating
            $filteredRatings = $ratings->filter(function ($rating) use ($existingUser) {
                return optional($rating->booking)->user_id === $existingUser->id;
            });

            $passenger_total_ratings = $filteredRatings->count();
            $totalAverage = $filteredRatings->avg('average_rating');
            $passenger_average_rating = $totalAverage;

            $ratings = Rating::where(function ($query) use ($existingUser) {
                // Ratings where type is 2 and user_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($existingUser) {
                          $query->where('user_id', $existingUser->id);
                      });
                // OR Ratings where type is 1 and ride_id belongs to the user
                $query->orWhere(function ($query) use ($existingUser) {
                    $query->where('type', '1')
                          ->whereHas('ride', function ($query) use ($existingUser) {
                              $query->where('added_by', $existingUser->id);
                          });
                });
            })->get();

            $user_total_ratings = $ratings->count();
            $totalAverage = $ratings->avg('average_rating');
            $user_average_rating = $totalAverage;

            if ($existingUser->gender === 'male') {
                $existingUser->gender_label = $genderLabel->male_option_label;
            } elseif ($existingUser->gender === 'female') {
                $existingUser->gender_label = $genderLabel->female_option_label;
            } elseif ($existingUser->gender === 'prefer not to say') {
                $existingUser->gender_label = $genderLabel->prefer_option_label;
            }

            $data = ['token' => $token, 'first_name' => $existingUser->first_name, 'last_name' => $existingUser->last_name, 'gender' => $existingUser->gender, 'gender_label' => $existingUser->gender_label, 'profile_image' => $existingUser->profile_image, 'profile_original_image' => $existingUser->profile_original_image, 'email' => $existingUser->email, 'step' => $existingUser->step, 'driver' => $existingUser->driver,'id' => $existingUser->id, 'driver_average_rating' => $driver_average_rating, 'passenger_average_rating' => $passenger_average_rating, 'user_average_rating' => $user_average_rating, 'driver_total_ratings' => $driver_total_ratings, 'passenger_total_ratings' => $passenger_total_ratings, 'langId' => isset($existingUser->lang_id) ? $existingUser->lang_id : $defaultLangId,'user_total_ratings' => $user_total_ratings];
            return $this->successResponse($data, 'Login successfully!');
        }

        // Split the full name into first and last names
        $nameParts = explode(' ', $request->user_name, 2); // Split into two parts
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Set to empty string if no last name

        // If the user is not registered, create a new user
        $newUser = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'email_verified' => '1',
            'password' => '',
            'profile_image' => $request->photourl,
            'provider' => $request->type,
            'provider_id' => $request->type_id,
            'lang_id' => $defaultLangId
        ]);

        auth()->login($newUser);

        $token = $newUser->createToken('ridesharing')->plainTextToken;

        $ratings = Rating::where('status', 1)->where('type', '1')->get();
        // Calculate average rating
        $filteredRatings = $ratings->filter(function ($rating) use ($newUser) {
            return optional($rating->ride)->added_by === $newUser->id;
        });

        $driver_total_ratings = $filteredRatings->count();
        $totalAverage = $filteredRatings->avg('average_rating');
        $driver_average_rating = $totalAverage;

        $ratings = Rating::where('status', 1)->where('type', '2')->get();
        // Calculate average rating
        $filteredRatings = $ratings->filter(function ($rating) use ($newUser) {
            return optional($rating->booking)->user_id === $newUser->id;
        });

        $passenger_total_ratings = $filteredRatings->count();
        $totalAverage = $filteredRatings->avg('average_rating');
        $passenger_average_rating = $totalAverage;
        
        $ratings = Rating::where(function ($query) use ($newUser) {
            // Ratings where type is 2 and user_id belongs to the user
            $query->where('type', '2')
                  ->whereHas('booking', function ($query) use ($newUser) {
                      $query->where('user_id', $newUser->id);
                  });
            // OR Ratings where type is 1 and ride_id belongs to the user
            $query->orWhere(function ($query) use ($newUser) {
                $query->where('type', '1')
                      ->whereHas('ride', function ($query) use ($newUser) {
                          $query->where('added_by', $newUser->id);
                      });
            });
        })->get();

        $user_total_ratings = $ratings->count();
        $totalAverage = $ratings->avg('average_rating');
        $user_average_rating = $totalAverage;

        if ($newUser->gender === 'male') {
            $newUser->gender_label = $genderLabel->male_option_label;
        } elseif ($newUser->gender === 'female') {
            $newUser->gender_label = $genderLabel->female_option_label;
        } elseif ($newUser->gender === 'prefer not to say') {
            $newUser->gender_label = $genderLabel->prefer_option_label;
        }

        $data = ['token' => $token, 'first_name' => $newUser->first_name, 'last_name' => $newUser->last_name, 'gender' => $newUser->gender, 'gender_label' => $newUser->gender_label, 'profile_image' => $newUser->profile_image, 'profile_original_image' => $newUser->profile_original_image, 'email' => $newUser->email, 'step' => $newUser->step, 'id' => $newUser->id, 'driver_average_rating' => $driver_average_rating, 'passenger_average_rating' => $passenger_average_rating, 'user_average_rating' => $user_average_rating, 'driver_total_ratings' => $driver_total_ratings, 'langId' => isset($newUser->lang_id) ? $newUser->lang_id : $defaultLangId, 'passenger_total_ratings' => $passenger_total_ratings, 'user_total_ratings' => $user_total_ratings];
        return $this->successResponse($data, 'Login successfully!');
    }

    public function updateUserLanguage(Request $request)
    {

        $getUser = Auth::guard('sanctum')->user();
        if(isset($getUser) && !empty($getUser)){
            $getUser->lang_id = $request->lang_id;
            $getUser->save();
        }

        $data = [];
        return $this->successResponse($data, 'User Language update successfully');
    }

    public function checkStatus(Request $request)
    {
        $getUser = Auth::guard('sanctum')->user();
        
        $data = ['status' => $getUser->admin_deactive_account];
        return $this->successResponse($data, 'Admin closed your account due to cancellation policy');
    }
}
