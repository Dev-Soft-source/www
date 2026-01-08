<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\NewVehicleAddedMail;
use App\Mail\VehicleRemovedEmail;
use App\Services\FCMService;
use App\Models\FeaturesSettingDetail;
use App\Models\FCMToken;
use App\Models\Language;
use App\Models\Vehicle;
use App\Models\Ride;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MyVehicleSettingDetail;
use App\Models\Notification;
use App\Models\PostRidePageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileVehicleController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;
        $vehicles = Vehicle::where('user_id',$user_id)->orderBy('primary_vehicle', 'desc')->orderBy('id', 'desc')->get();

        $vehicleSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $vehicleSettingPage = MyVehicleSettingDetail::where('language_id', $request->lang_id)->first();
            $postRidePage = PostRidePageSettingDetail::where('language_id', $request->lang_id)->first();
            $vehicleSettingPage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
            $vehicleSettingPage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
            $vehicleSettingPage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
            $vehicleSettingPage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
            $vehicleSettingPage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
            $vehicleSettingPage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
            $vehicleSettingPage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
            $vehicleSettingPage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
            $vehicleSettingPage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($request->lang_id)->value('name');
            $vehicleSettingPage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
            $vehicleSettingPage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($request->lang_id)->value('name');
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('delete_vehicle_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $vehicleSettingPage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $vehicleSettingPage->vehicle_type_convertible_value = $postRidePage->vehicle_type_convertible_text;
                $vehicleSettingPage->vehicle_type_convertible_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_convertible_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_hatchback_value = $postRidePage->vehicle_type_hatchback_text;
                $vehicleSettingPage->vehicle_type_hatchback_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_hatchback_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_coupe_value = $postRidePage->vehicle_type_coupe_text;
                $vehicleSettingPage->vehicle_type_coupe_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_coupe_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_minivan_value = $postRidePage->vehicle_type_minivan_text;
                $vehicleSettingPage->vehicle_type_minivan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_minivan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_sedan_value = $postRidePage->vehicle_type_sedan_text;
                $vehicleSettingPage->vehicle_type_sedan_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_sedan_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_station_wagon_value = $postRidePage->vehicle_type_station_wagon_text;
                $vehicleSettingPage->vehicle_type_station_wagon_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_station_wagon_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_suv_value = $postRidePage->vehicle_type_suv_text;
                $vehicleSettingPage->vehicle_type_suv_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_suv_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_truck_value = $postRidePage->vehicle_type_truck_text;
                $vehicleSettingPage->vehicle_type_truck_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_truck_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $vehicleSettingPage->vehicle_type_van_value = $postRidePage->vehicle_type_van_text;
                $vehicleSettingPage->vehicle_type_van_text = FeaturesSettingDetail::whereFeaturesSettingId($postRidePage->vehicle_type_van_text)->whereLanguageId($selectedLanguage->id)->value('name');
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('delete_vehicle_message')->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'file' => trans('validation.file'),
            'mimes' => trans('validation.mimes'),
            'max' => trans('validation.max.file'),
        ];

        $data = ['vehicles' => $vehicles, 'vehicleSettingPage' => $vehicleSettingPage, 'messages' => $messages, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get vehicles successfully');
    }

    public function store(Request $request){
        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_add_message', 'image_size_error_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_add_message', 'image_size_error_message')->first();
            }
        }

        $customMessages = [
            'file.max' => $message->image_size_error_message,
        ];
        $validated = $request->validate([
            'make' => 'required',
            'model' => 'required',
            'type' => 'required',
            'liscense_no' => 'required|max:8',
            'color' => 'required|max:15',
            'year' => 'required|max:4',
            'car_type' => 'required',
            'image' => 'file|mimes:jpeg,png,gif|max:10240',
        ], $customMessages);

        
        $user = Auth::guard('sanctum')->user();

        $primaryVehicle = 0;

        // Check if this is user's first vehicle - auto-set as primary
        $userVehicleCount = Vehicle::where('user_id', $user->id)->count();
        if ($userVehicleCount == 0) {
            $primaryVehicle = 1;
        } elseif (isset($request->primary_vehicle) && $request->primary_vehicle != "") {
            // If user is setting this vehicle as primary, clear other vehicles' primary status
            DB::table('vehicles')->where('user_id', $user->id)->update(['primary_vehicle' => 0]);
            $primaryVehicle = $request->primary_vehicle;
        }

        $filename = "";
        $filenameOriginal = "";
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path,$filename);

            $fileOriginal = $request->file('original_image');
            $filenameOriginal = $fileOriginal->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $fileOriginal->move($destination_path,$filenameOriginal);

        } else {
            $filename = '';
            $filenameOriginal = "";
        }

        
        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'make' => $request->make,
            'model' => $request->model,
            'type' => $request->type,
            'liscense_no' => $request->liscense_no,
            'color' => $request->color,
            'year' => $request->year,
            'car_type' => $request->car_type,
            'image' => $filename,
            'original_image' => $filenameOriginal,
            'remove_image' => $request->remove_image ? $request->remove_image : 0,
            'primary_vehicle' => $primaryVehicle
        ]);

        if ($user->email_notification == 1) {
            $emailData = [
                'first_name' => $user->first_name,
            ];
            Mail::to($user->email)->send(new NewVehicleAddedMail($emailData));
        }
         $notification = Notification::create([
        'type' => null, 
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

        $data = ['vehicle' => $vehicle];
        return $this->successResponse($data, strip_tags($message->vehicle_add_message));
    }

    public function edit(Request $request){
        $vehicle = Vehicle::whereId($request->id)->first();

        $data = ['vehicle' => $vehicle];
        return $this->successResponse($data, 'Get vehicle successfully');
    }

    public function update(Request $request){
        $validated = $request->validate([
            'make' => 'required',
            'model' => 'required',
            'type' => 'required',
            'liscense_no' => 'required|max:8',
            'color' => 'required|max:15',
            'year' => 'required|max:4',
            'car_type' => 'required',
        ]);

        $user = Auth::guard('sanctum')->user();
        $primaryVehicle = 0;

        if(isset($request->primary_vehicle) && $request->primary_vehicle != ""){
            // Only update vehicles belonging to this user (not all vehicles)
            DB::table('vehicles')->where('user_id', $user->id)->update(['primary_vehicle' => 0]);
            $primaryVehicle = 1;
        }

        // Get vehicle before update to access old image paths
        $vehicle = Vehicle::whereId($request->id)->first();

        // Handle image removal if requested
        if (isset($request->remove_image) && $request->remove_image == "1") {
            // Delete old image files from filesystem
            if ($vehicle->image) {
                $imagePath = public_path('/car_images/' . $vehicle->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            if ($vehicle->original_image) {
                $originalImagePath = public_path('/car_images/' . $vehicle->original_image);
                if (file_exists($originalImagePath)) {
                    unlink($originalImagePath);
                }
            }

            // Update rides to mark car image as removed
            $getRides = Ride::where('vehicle_id', $vehicle->id)->get();
            foreach ($getRides as $getRide) {
                $getRide->remove_car_image = 1;
                $getRide->save();
            }

            // Clear image fields
            Vehicle::whereId($request->id)->update([
                'image' => null,
                'original_image' => null,
                'remove_image' => 1
            ]);
        }

        Vehicle::whereId($request->id)->update([
            'make' => $request->make,
            'model' => $request->model,
            'type' => $request->type,
            'liscense_no' => $request->liscense_no,
            'color' => $request->color,
            'year' => $request->year,
            'car_type' => $request->car_type,
            'primary_vehicle' => $primaryVehicle
        ]);

        if ($request->hasFile('image')) {
            $customMessages = [
                'max' => 'Can not upload image size greater than 10MB',
            ];
            $request->validate([
                'image' => 'file|mimes:jpeg,png,gif|max:10240',
            ], $customMessages);

            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path,$filename);

            $fileOriginal = $request->file('original_image');
            $filenameOriginal = $fileOriginal->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $fileOriginal->move($destination_path,$filenameOriginal);

            Vehicle::whereId($request->id)->update([
                'image' => $filename,
                'original_image' => $filenameOriginal
            ]);
        }

        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_update_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_update_message')->first();
            }
        }

        $vehicle = Vehicle::whereId($request->id)->first();
        $data = ['vehicle' => $vehicle];
        return $this->successResponse($data, strip_tags($message->vehicle_update_message));
    }

    public function destroy(Request $request)
    {
        $message = null;
        $vehicle = Vehicle::whereId($request->id)->first();

        if ($vehicle) {
            // Check if we're deleting the primary vehicle
            $wasPrimary = $vehicle->primary_vehicle == '1' || $vehicle->primary_vehicle == 1;
            $userId = $vehicle->user_id;

            $vehicle->delete();

            // If we deleted the primary vehicle, set the most recent remaining vehicle as primary
            if ($wasPrimary) {
                $firstRemainingVehicle = Vehicle::where('user_id', $userId)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($firstRemainingVehicle) {
                    $firstRemainingVehicle->update(['primary_vehicle' => '1']);
                }
            }

            $user = Auth::guard('sanctum')->user();

            $emailData = [
                'first_name' => $user->first_name,
            ];
            if (isset($user->email_notification) && $user->email_notification == 1) {
                Mail::to($user->email)->send(new VehicleRemovedEmail($emailData));
            }

               $notification = Notification::create([
            'type' => null, 
            'category' => 'system',
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

            $selectedLanguage = app()->getLocale();
            if ($selectedLanguage) {
                // Find the language by abbreviation
                $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

                if ($selectedLanguage) {
                    // Retrieve the HomePageSettingDetail associated with the selected language
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_removed_message', 'general_error_message')->first();
                }
            } else {
                $selectedLanguage = Language::where('is_default', 1)->first();
                if ($selectedLanguage) {
                    $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('vehicle_removed_message', 'general_error_message')->first();
                }
            }

            return $this->successResponse([], strip_tags($message->vehicle_removed_message));
        }
        return $this->apiErrorResponse(strip_tags($message->general_error_message ?? 'The vehicle id is missing'), 200);
    }
}