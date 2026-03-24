<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Mail\DriverLicenseUploadMail;
use App\Models\Admin;
use App\Models\Country;
use App\Models\User;
use App\Models\DriverSettingDetail;
use App\Models\Language;
use App\Models\SuccessMessagesSettingDetail;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyDriverController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $user = Auth::guard('sanctum')->user();

        $driverSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $driverSettingPage = DriverSettingDetail::where('language_id', $request->lang_id)->first();
            $messages = SuccessMessagesSettingDetail::where('language_id', $request->lang_id)->select('remove_driver_license_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $driverSettingPage = DriverSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('remove_driver_license_message')->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'file' => trans('validation.file'),
            'mimes' => trans('validation.mimes'),
            'max' => trans('validation.max.file'),
        ];

        $data = ['user' => $user, 'driverSettingPage' => $driverSettingPage, 'messages' => $messages, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get user successfully');
    }

    public function update(Request $request){
        $customMessages = [
            'max' => 'Can not upload image size greater than 10MB',
        ];
        $request->validate([
            'driver_liscense' => 'required|file|mimes:pdf,jpeg,jpg,png,gif|max:10240',
        ], $customMessages);

        $userId = $request->id ?? Auth::guard('sanctum')->id();
        $user = User::whereId($userId)->firstOrFail();

        if ($request->hasFile('driver_liscense')) {
            $file = $request->file('driver_liscense');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/driver_liscenses');
            if (!is_dir($destination_path)) {
                mkdir($destination_path, 0755, true);
            }
            $file->move($destination_path,$filename);

            $fileOriginal = $request->file('driver_license_original_upload');
            $filenameOriginal = $filename;
            if ($fileOriginal) {
                $filenameOriginal = $fileOriginal->getClientOriginalName();
                $fileOriginal->move($destination_path,$filenameOriginal);
            }

            User::whereId($userId)->update([
                'driver_liscense' => $filename,
                'driver_license_original_upload' => $filenameOriginal,
                'driver_license_upload' => Carbon::now(),
                'driver' => 2,
            ]);
        }

        $user = User::whereId($userId)->first();
        $country = Country::whereId($user->country)->first();
        $admin = Admin::first();

        if ($admin && !empty($admin->admin_email)) {
            $data = [
                'username' => $admin->username ?? 'Admin',
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $country->name ?? '',
                'driver_liscense' => $user->driver_liscense,
                'upload_date' => \Carbon\Carbon::now()->format('M d, Y H:i:s'),
            ];
            try {
                Mail::to($admin->admin_email)->queue(new DriverLicenseUploadMail($data));
            } catch (\Throwable $exception) {
                Log::warning('Driver license upload email queue failed', [
                    'user_id' => $userId,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $message = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('license_upload_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('license_upload_message')->first();
            }
        }

        $user = User::whereId($userId)->first();
        $data = ['user' => $user];
        return $this->successResponse(
            $data,
            strip_tags($message->license_upload_message ?? 'Driver license uploaded successfully')
        );
    }

    public function remove(){

        
        $selectedLanguage = app()->getLocale();
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('license_delete_message')->first();


        $user_id = Auth::guard('sanctum')->user()->id;

        User::whereId($user_id)->update([
            'driver_liscense' => NULL,
            'driver_license_upload' => NULL,
            'driver_license_original_upload' => NULL,
            'driver' => 0,
        ]);

        return $this->successResponse('', strip_tags($message->license_delete_message ?? 'Driver license removed successfully'));
    }
}
