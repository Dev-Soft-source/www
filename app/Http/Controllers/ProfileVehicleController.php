<?php

namespace App\Http\Controllers;

use App\Mail\NewVehicleAddedMail;
use App\Services\FCMService;
use App\Models\FCMToken;
use App\Mail\VehicleRemovedEmail;
use App\Models\Language;
use App\Models\MyVehicleSettingDetail;
use App\Models\Notification;
use App\Models\Ride;
use App\Models\FeaturesSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Models\PostRidePageSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfileVehicleController extends Controller
{
    public function index(Request $request ,$lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $myVehiclePage = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $vehicles = Vehicle::where('user_id',$user_id)->orderBy('id', 'desc')->get();

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

            return view('profile_vehicle',['vehicles' => $vehicles ,'reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'myVehiclePage' => $myVehiclePage,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function create(Request $request ,$lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $myVehiclePage = null;
        $postRidePage = null;
        $messages = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_vehicle_message','no_go_back_button_text','yes_remove_it_button_text')->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_vehicle_message','no_go_back_button_text','yes_remove_it_button_text')->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        
        $myVehiclePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
        $myVehiclePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
        $myVehiclePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
        $myVehiclePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
        $myVehiclePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
        $myVehiclePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
        $myVehiclePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
        $myVehiclePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
        $myVehiclePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
        $myVehiclePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');

        $notifications = null;
        $userVehicleCount = 0;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $userVehicleCount = Vehicle::where('user_id', $user_id)->count();
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
        
        return view('create_vehicle',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage,'myVehiclePage' => $myVehiclePage, 'messages' => $messages, 'userVehicleCount' => $userVehicleCount]);
    }

    public function store(Request $request){
        $message = null;
        $myVehiclePage = null;
        $niceNames = [];
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_add_message', 'image_size_error_message')->first();
                $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'make' => isset($myVehiclePage->make_error) ? $myVehiclePage->make_error : '',
                    'model' => isset($myVehiclePage->model_error) ? $myVehiclePage->model_error : '',
                    'type' => isset($myVehiclePage->vehicle_type_error) ? $myVehiclePage->vehicle_type_error : '',
                    'liscense_no' => isset($myVehiclePage->color_error) ? $myVehiclePage->color_error : '',
                    'color' => isset($myVehiclePage->license_error) ? $myVehiclePage->license_error : '',
                    'year' => isset($myVehiclePage->year_error) ? $myVehiclePage->year_error : '',
                    'car_type' => isset($myVehiclePage->fuel_error) ? $myVehiclePage->fuel_error : '',
                    'primary_vehicle' => isset($myVehiclePage->set_primary_error) ? $myVehiclePage->set_primary_error : '',
                    'image' => isset($myVehiclePage->photo_error) ? $myVehiclePage->photo_error : '',
                ];
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_add_message', 'image_size_error_message')->first();
                $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $niceNames = [
                    'make' => isset($myVehiclePage->make_error) ? $myVehiclePage->make_error : '',
                    'model' => isset($myVehiclePage->model_error) ? $myVehiclePage->model_error : '',
                    'type' => isset($myVehiclePage->vehicle_type_error) ? $myVehiclePage->vehicle_type_error : '',
                    'liscense_no' => isset($myVehiclePage->color_error) ? $myVehiclePage->color_error : '',
                    'color' => isset($myVehiclePage->license_error) ? $myVehiclePage->license_error : '',
                    'year' => isset($myVehiclePage->year_error) ? $myVehiclePage->year_error : '',
                    'car_type' => isset($myVehiclePage->fuel_error) ? $myVehiclePage->fuel_error : '',
                    'primary_vehicle' => isset($myVehiclePage->set_primary_error) ? $myVehiclePage->set_primary_error : '',
                    'image' => isset($myVehiclePage->photo_error) ? $myVehiclePage->photo_error : '',
                ];
            }
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path,$filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } else {
            $filename = '';
        }

        $validator = Validator::make($request->all(),[
            'make' => 'required',
            'model' => 'required',
            'type' => 'required',
            'liscense_no' => 'required|max:8',
            'color' => 'required|max:15',
            'year' => 'required|max:4',
            'car_type' => 'required',
            'primary_vehicle' => 'required',
            'image' => $request->has('existing_image') ? 'nullable' : 'file|mimes:jpeg,png,jpg,gif|max:10240',
        ], [], $niceNames);

        if ($validator->fails()) {
            // Check if there are validation errors for the 'uploaded' attribute
            $hasRequiredError = $validator->errors()->has('image') && ($validator->errors()->first('image') === 'The image is not uploaded yet' || $validator->errors()->first('image') === 'The image failed to upload');
            // If there are other validation errors or the 'image' error is not present, return back with errors and the uploaded image path
            if (!$hasRequiredError || $validator->errors()->count() > 1) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('uploaded_image', $filename ?? null);
            }
        }

        $remove_image = $request->filled('remove_image') ? $request->remove_image : 0;

        if(isset($request->primary_vehicle) && $request->primary_vehicle === "1"){
            Vehicle::where('user_id', auth()->user()->id)->update(['primary_vehicle' => 0]);
        }

        // Check if this is user's first vehicle and auto-set as primary
        $userVehicleCount = Vehicle::where('user_id', auth()->user()->id)->count();
        $primaryVehicleValue = $request->primary_vehicle;
        
        // If this is the first vehicle, automatically set it as primary
        if ($userVehicleCount == 0) {
            $primaryVehicleValue = '1';
        }

        Vehicle::create([
            'user_id' => auth()->user()->id,
            'make' => $request->make,
            'model' => $request->model,
            'type' => $request->type,
            'liscense_no' => $request->liscense_no,
            'color' => $request->color,
            'year' => $request->year,
            'car_type' => $request->car_type,
            'primary_vehicle' => $primaryVehicleValue,
            'image' => $filename,
            'original_image' => $filename,
            'remove_image' => $remove_image,
        ]);
        $user = auth()->user();
        if ($user->email_notification == 1) {
        $emailData = [
            'first_name' => $user->first_name,
        ];
        Mail::to($user->email)->send(new NewVehicleAddedMail($emailData));
    }

      $notification = Notification::create([
        'type' => null,
        'category' => 'system', 
        'receiver_id' => $user->id,
        'posted_by' => $user->id, 
        'message' => 'A new vehicle added to your profile',
        'status' => 'completed',
        'notification_type' => 'vehicle'
    ]);
    
    // Send push notification
    $fcmService = new FCMService();
    $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
    $body = $notification->message;

    $fcmToken = $user->mobile_fcm_token;
    if ($fcmToken) {
        $fcmService->sendNotification($fcmToken, $body);
    }

    foreach ($fcm_tokens as $fcm_token) {
        try {
            $fcmService->sendNotification($fcm_token->token, $body);
        } catch (\Exception $e) {
            Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
        }
    }

        return redirect()->route('profile.vehicle', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->vehicle_add_message);
    }

    public function edit(Request $request ,$lang = null, $id){
        $languages = Language::all();
        // Store the selected language in the session
        $selectedLanguage = session('selectedLanguage');
        $myVehiclePage = null;
        $postRidePage = null;
        $messages = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_vehicle_message','no_go_back_button_text','yes_remove_it_button_text')->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $myVehiclePage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_vehicle_message','no_go_back_button_text', 'yes_remove_it_button_text')->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        $myVehiclePage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
        $myVehiclePage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
        $myVehiclePage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
        $myVehiclePage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
        $myVehiclePage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
        $myVehiclePage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
        $myVehiclePage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
        $myVehiclePage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
        $myVehiclePage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');
        $myVehiclePage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
        $myVehiclePage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');

        $vehicle = Vehicle::whereId($id)->first();

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
        
        return view('edit_vehicle',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'vehicle' => $vehicle,'myVehiclePage' => $myVehiclePage,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'myVehiclePage' => $myVehiclePage, 'messages' => $messages]);
    }

     public function update($id, Request $request){
        // Debugging: Log request data and file size
        \Log::info('Request data:', $request->all());
        if ($request->hasFile('image')) {
            \Log::info('Uploaded file size: ' . ($request->file('image')->getSize() / 1024) . ' KB');
        }

        $customMessages = [
            'mimes' => 'The :attribute must be a file of type: jpeg, png',
            'uploaded' => 'The image is not uploaded yet',
            'max' => 'Can not upload image size greater than 10MB',
        ];

        $vehicle = Vehicle::findOrFail($id);
        $filename = $vehicle->image;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->filled('remove_image') && $request->remove_image == 1) {
            $filename = null;
        }

        $validator = Validator::make($request->all(),[
            'make' => 'required',
            'model' => 'required',
            'type' => 'required',
            'liscense_no' => 'required|max:8',
            'color' => 'required|max:15',
            'year' => 'required|max:4',
            'primary_vehicle' => 'required',
            'car_type' => 'required',
            'image' => $vehicle->image || $request->filled('remove_image') ? 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240' : 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ], $customMessages);

        if ($validator->fails()) {
            // Check if there are validation errors for the 'uploaded' attribute
            $hasRequiredError = $validator->errors()->has('image') && ($validator->errors()->first('image') === 'The image is not uploaded yet' || $validator->errors()->first('image') === 'The image failed to upload');
            // If there are other validation errors or the 'image' error is not present, return back with errors and the uploaded image path
            if (!$hasRequiredError || $validator->errors()->count() > 1) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('uploaded_image', $filename ?? null);
            }
        }

        $remove_image = $request->filled('remove_image') ? $request->remove_image : 0;

        if(isset($request->primary_vehicle) && $request->primary_vehicle === "1"){
            Vehicle::where('user_id', auth()->user()->id)->update(['primary_vehicle' => 0]);
        }

       $getVehicle =  Vehicle::whereId($id)->update([
            'make' => $request->make,
            'model' => $request->model,
            'type' => $request->type,
            'liscense_no' => $request->liscense_no,
            'color' => $request->color,
            'year' => $request->year,
            'car_type' => $request->car_type,
            'primary_vehicle' => $request->primary_vehicle,
            'image' => $filename,
            'original_image' => $filename,
            'remove_image' => $remove_image,
        ]);

        if(isset($getVehicle->remove_image) && $getVehicle->remove_image != "0"){
            $getRides = Ride::where('vehicle_id', $getVehicle->id)->get();
            foreach ($getRides as $key => $getRide) {
                $getRide->remove_car_image = 1;
                $getRide->save();
            }
        }

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_update_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_update_message')->first();
            }
        }
        return redirect()->route('profile.vehicle', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->vehicle_update_message);
    }

    public function destroy($lang = null, $id)
    {
        // $user_id = auth()->user()->id;
        $user = auth()->user();
        $user_id = $user->id;
        $rides = Ride::where('added_by',$user_id)->where('vehicle_id',$id)->get();

        $message = null;
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_removed_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_removed_message')->first();
            }
        }

        // Check if any there is an upcoming ride has the same vehicle
        foreach ($rides as $existingRide) {
            if ($existingRide->date > now()->toDateString() || $existingRide->date == now()->toDateString() && $existingRide->time > now()->toTimeString()) {
                if ($existingRide->status !== '2') {
                    return redirect()->route('profile.vehicle', ['lang' => $selectedLanguage->abbreviation])->with('message', "You can't delete this vehicle because this vehicle is used in an upcoming ride");
                }
            }
        }

        // Check if we're deleting the primary vehicle
        $deletingVehicle = Vehicle::findOrFail($id);
        $wasPrimary = $deletingVehicle->primary_vehicle == '1';
        
        $result = Vehicle::whereId($id)->delete();
        if ($result) {
            // If we deleted the primary vehicle, set the first remaining vehicle as primary
            if ($wasPrimary) {
                $firstRemainingVehicle = Vehicle::where('user_id', auth()->user()->id)->first();
                if ($firstRemainingVehicle) {
                    $firstRemainingVehicle->update(['primary_vehicle' => '1']);
                }
            }
            $emailData = [
                'first_name' => $user->first_name,
            ];
            if (isset($user->email_notification) && $user->email_notification == 1) {
            Mail::to($user->email)->send(new VehicleRemovedEmail($emailData));
            }

            $notification = Notification::create([
                'type' => null, 
                'receiver_id' => $user->id,
                'posted_by' => $user->id, 
                'message' => ' Vehicle removed from your profile',
                'status' => 'completed',
                'notification_type' => 'vehicle'
            ]);
            
            // Send push notification
            $fcmService = new FCMService();
            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
            $body = $notification->message;
    
            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }
    
            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
                }
            }

            return redirect()->route('profile.vehicle', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->vehicle_removed_message);
        }
    }
}
