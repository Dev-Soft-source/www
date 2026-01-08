<?php

namespace App\Http\Controllers;

use App\Mail\DriverLicenseUploadMail;
use App\Models\Admin;
use App\Models\Country;
use App\Models\DriverSettingDetail;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VerifyDriverController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $driverSettingPage = null;
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $driverSettingPage = DriverSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $driverSettingPage = DriverSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();

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

            return view('verify_driver',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'driverSettingPage' => $driverSettingPage,'user' => $user,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation]);
        }
    }

    public function update($id, Request $request){
        $customMessages = [
            'uploaded' => 'The image is not uploaded yet',
            'max' => 'Can not upload image size greater than 10MB',
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/driver_liscenses');
            $file->move($destination_path,$filename);
        } elseif ($request->hasFile('existing_image')) {
            $file = $request->file('existing_image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/driver_liscenses');
            $file->move($destination_path,$filename);
        } elseif ($request->has('existing_image') && $request->input('existing_image') !== null) {
            $filename = $request->input('existing_image');
        }

        $validator = Validator::make($request->all(),[
            'image' => $request->input('existing_image') !== null ? 'nullable' : 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ], $customMessages);

        if ($validator->fails()) {
            // Check if there are validation errors for the 'uploaded' attribute
            $hasRequiredError = $validator->errors()->has('image') && $validator->errors()->first('image') === 'The image is not uploaded yet';
            // If there are other validation errors or the 'image' error is not present, return back with errors and the uploaded image path
            if (!$hasRequiredError || $validator->errors()->count() > 1) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('uploaded_image', $filename ?? null);
            }
        }

        User::whereId($id)->update([
            'driver_liscense' => $filename,
            'driver_license_original_upload' => $filename,
            'driver_license_upload' => Carbon::now(),
            'driver' => 2,
        ]);

        $user = User::whereId($id)->first();
        $country = Country::whereId($user->country)->first();
        $admin = Admin::first();

        $data = ['username' => $admin->username,'first_name' => $user->first_name,'last_name' => $user->last_name,'email' => $user->email,'phone' => $user->phone,'country' => $country->name];
        // Send upload email
        Mail::to($admin->admin_email)->queue(new DriverLicenseUploadMail($data));

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('license_upload_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('license_upload_message')->first();
            }
        }
        return redirect()->route('driver.verify', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->license_upload_message);
    }


    public function remove(Request $request){

        $user_id = auth()->user()->id;
        
        User::whereId($user_id)->update([
            'driver_liscense' => NUll,
            'driver_license_original_upload' => NULL,
            'driver_license_upload' => NULL,
            'driver' => 0,
        ]);
        
        return response()->json(['status' => 'success']);
    }
}
