<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\Language;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilePhotoController extends Controller
{
    use StatusResponser;

    public function index(){        
        $user = Auth::guard('sanctum')->user();

        $validationMessages = [
            'required' => trans('validation.required'),
            'file' => trans('validation.file'),
            'mimes' => trans('validation.mimes'),
            'max' => trans('validation.max.file'),
        ];

        $data = ['user' => $user, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get user successfully');
    }

    public function update(Request $request){
        $selectedLanguage = app()->getLocale();
        $message = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('profile_photo_update_message', 'general_error_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('profile_photo_update_message', 'general_error_message')->first();
            }
        }
        
        $user = Auth::guard('sanctum')->user();

        $customMessages = [
            'max' => 'Can not upload image size greater than 10MB',
        ];

        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,gif|max:10240',
        ], $customMessages);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/users_images');
            $file->move($destination_path,$filename);

            $fileOriginal = $request->file('profile_original_image');
            $filenameOriginal = $fileOriginal->getClientOriginalName();
            $destination_path = public_path('/users_images');
            $fileOriginal->move($destination_path,$filenameOriginal);
            User::whereId($user->id)->update([
                'profile_image' => $filename,
                'profile_original_image' => $filenameOriginal,
            ]);
        }

        $user = User::whereId($user->id)->first();
        $data = ['user' => $user];
        return $this->successResponse($data, strip_tags($message->profile_photo_update_message ?? 'Profile photo updated successfully'));
    }
}