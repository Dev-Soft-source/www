<?php

namespace App\Http\Controllers;

use App\Mail\NewVehicleAddedMail;
use App\Services\FCMService;
use App\Models\FCMToken;
use App\Models\FeaturesSettingDetail;
use App\Mail\VehicleRemovedEmail;
use App\Models\Language;
use App\Models\MyVehicleSettingDetail;
use App\Models\Notification;
use App\Models\Ride;
use App\Models\MyReviewSettingDetail;
use App\Models\PostRidePageSettingDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfileVehicleController extends Controller
{
    public function index(Request $request, $lang = null)
    {

        $myVehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $vehicles = Vehicle::where('user_id', $user_id)->orderBy('id', 'desc')->get();

            return view('profile_vehicle', [
                'vehicles' => $vehicles,
                'reviewSetting' => $reviewSetting,
                'ProfilePage' => $ProfilePage,
                'ProfileSetting' => $ProfileSetting,
                'myVehiclePage' => $myVehiclePage
            ]);
        } else {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation]);
        }
    }

    public function create(Request $request, $lang = null)
    {
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $myVehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        // $myVehiclePage = $this->mapVehicleTypeFields($myVehiclePage, $postRidePage);
        $userVehicleCount = 0;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $userVehicleCount = Vehicle::where('user_id', $user_id)->count();
        }

        return view('create_vehicle', [
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'myVehiclePage' => $myVehiclePage,
            'userVehicleCount' => $userVehicleCount,
        ]);
    }

    public function store(Request $request)
    {
        $message = $this->successMessage;

        $validator = Validator::make($request->all(), [
            'make' => 'required',
            'model' => 'required',
            'vehicle_type' => 'required|integer|exists:features_setting_detail,features_setting_id',
            'license_no' => 'required|max:8',
            'color' => 'required|max:15',
            'year' => 'required|max:4',
            'power_type' => 'required',
            'primary_vehicle' => 'required',
            'vehicle_image' => 'required_without:existing_image|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [], []);

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

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } else {
            $filename = '';
        }

        $remove_image = $request->filled('remove_image') ? $request->remove_image : 0;

        if (isset($request->primary_vehicle) && $request->primary_vehicle === "1") {
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
            'type' => Vehicle::normalizeVehicleTypeId($request->type),
            'license_no' => $request->license_no,
            'color' => $request->color,
            'year' => $request->year,
            'power_type' => $request->power_type,
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
            Mail::to($user->email)->queue(new NewVehicleAddedMail($emailData));
        }

        // User::whereId($user->id)->update([
        //     'step3' => 1
        // ]);

        $notification = Notification::create([
            'type' => null,
            'category' => 'system',
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' => getNotificationMessageText(
                'vehicle_added_to_profile',
                $user,
                [],
                'A new vehicle added to your profile'
            ),
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

        return redirect()->route('profile.vehicle', ['lang' => $this->selectedLanguage->abbreviation])->with('message', $message->vehicle_add_message);
    }

    public function edit(Request $request, $lang = null, $id)
    {
        session()->forget('message');

        $myVehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        // $myVehiclePage = $this->mapVehicleTypeFields($myVehiclePage, $postRidePage);

        $vehicle = Vehicle::findOrFail($id);

        return view('edit_vehicle', [
            'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,
            'vehicle' => $vehicle,
            'myVehiclePage' => $myVehiclePage,
        ]);
    }

    public function update($id, Request $request)
    {
        $customMessages = [
            'mimes' => 'The :attribute must be a file of type: jpeg, png',
            'uploaded' => 'The image is not uploaded yet',
            'image.max' => 'Can not upload image size greater than 10MB',
            'license_no.max' => 'License plate number cannot exceed 8 characters.',
            'color.max' => 'Color cannot exceed 15 characters.',
            'year.max' => 'Year cannot exceed 4 characters.',
        ];

        $vehicle = Vehicle::findOrFail($id);
        // Get the raw attribute value (filename only) instead of the accessor (full URL)
        $attributes = $vehicle->getAttributes();
        $filename = isset($attributes['image']) && !empty($attributes['image']) ? $attributes['image'] : null;

        // Store old filename for potential deletion
        $oldFilename = isset($attributes['image']) && !empty($attributes['image']) ? $attributes['image'] : null;

        $validator = Validator::make($request->all(), [
            'make' => 'required',
            'model' => 'required',
            'vehicle_type' => 'required|integer|exists:features_setting_detail,features_setting_id',
            'license_no' => 'required|max:8',
            'color' => 'required|max:15',
            'year' => 'required|max:4',
            'primary_vehicle' => 'required',
            'power_type' => 'required',
            'image' => 'required_without:existing_image|image|mimes:jpeg,png,jpg,gif|max:10240'
            // 'image' => $vehicle->image || $request->filled('remove_image') ? 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240' : 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
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

        // Handle new image upload - check this FIRST before anything else
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();

            // Generate new filename with timestamp to ensure uniqueness
            $filename = time() . '_' . $originalName;
            $destination_path = public_path('/car_images');

            // Delete old file if it exists and is different from new filename
            if ($oldFilename && $oldFilename !== $filename && file_exists($destination_path . '/' . $oldFilename)) {
                @unlink($destination_path . '/' . $oldFilename);
            }

            // Move new file
            $file->move($destination_path, $filename);
        } elseif ($request->filled('remove_image') && $request->remove_image == 1) {
            // Handle image removal - delete the file
            if ($oldFilename && file_exists(public_path('/car_images/' . $oldFilename))) {
                @unlink(public_path('/car_images/' . $oldFilename));
            }
            $filename = null;
        } elseif ($request->filled('existing_image')) {
            // Only use existing_image if no new file was uploaded
            // existing_image should be just the filename (not full URL)
            $existingImage = $request->input('existing_image');
            // Make sure it's just a filename, not a full path
            $filename = basename($existingImage);
        }

        $remove_image = $request->filled('remove_image') ? $request->remove_image : 0;

        if (isset($request->primary_vehicle) && $request->primary_vehicle === "1") {
            Vehicle::where('user_id', auth()->user()->id)->update(['primary_vehicle' => 0]);
        }

        // Always update the vehicle, including image fields
        // This ensures the update happens even if the filename appears the same
        $updateData = [
            'make' => $request->make,
            'model' => $request->model,
            'type' => Vehicle::normalizeVehicleTypeId($request->vehicle_type),
            'license_no' => $request->license_no,
            'color' => $request->color,
            'year' => $request->year,
            'car_type' => $request->car_type,
            'primary_vehicle' => $request->primary_vehicle,
            'image' => $filename, // Always include image, even if it's the same
            'original_image' => $filename, // Always include original_image
            'remove_image' => $remove_image,
        ];

        $getVehicle = Vehicle::whereId($id)->update($updateData);
        // \Log::info('Vehicle updated with image: ' . ($filename ?? 'null'));

        if (isset($getVehicle->remove_image) && $getVehicle->remove_image != "0") {
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
        // todo: if the vehicle is primary and there are other vehicles, set the first remaining vehicle as primary after deletion
        // todo: if there is no vehicle left after deletion, do step3 = 0 for the user

        // $user_id = auth()->user()->id;
        $user = auth()->user();
        $user_id = $user->id;

        $message = null;
        $languages = Language::getAllCached();
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

        $today = now()->toDateString();
        $currentTime = now()->toTimeString();
        $hasUpcomingRide = Ride::where('added_by', $user_id)
            ->where('vehicle_id', $id)
            ->where('status', '!=', '2')
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('date', '>', $today)
                    ->orWhere(function ($subQuery) use ($today, $currentTime) {
                        $subQuery->where('date', $today)
                            ->where('time', '>', $currentTime);
                    });
            })
            ->exists();

        if ($hasUpcomingRide) {
            $defaultLang = Language::where('is_default', 1)->first();
            $myVehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($selectedLanguage->id, $defaultLang?->id ?? $selectedLanguage->id);
            $cannotDeleteMessage = $myVehiclePage->cannot_delete_vehicle_upcoming_ride_message ?? "You can't delete this vehicle because this vehicle is used in an upcoming ride";
            return redirect()->route('profile.vehicle', ['lang' => $selectedLanguage->abbreviation])->with('message', $cannotDeleteMessage);
        }

        // Check if we're deleting the primary vehicle
        $deletingVehicle = Vehicle::findOrFail($id);
        $wasPrimary = $deletingVehicle->primary_vehicle == '1';

        $result = Vehicle::whereId($id)->delete();
        if ($result) {
            // If we deleted the primary vehicle, set the first remaining vehicle (chronologically by id) as primary
            if ($wasPrimary) {
                $firstRemainingVehicle = Vehicle::where('user_id', auth()->user()->id)
                    ->orderBy('id', 'asc')
                    ->first();
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
                'message' => getNotificationMessageText(
                    'vehicle_removed_from_profile',
                    $user,
                    [],
                    'Vehicle removed from your profile'
                ),
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
