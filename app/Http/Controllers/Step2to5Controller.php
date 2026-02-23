<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;
use App\Models\Step2PageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Step2to5Controller extends Controller
{
    public function create($lang = null){
        $user = auth()->user();
        
        $step2Page = Step2PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('step2to5',[
            'step2Page' => $step2Page, 
            'user' => $user]);
    }

    public function update($id, Request $request){
        $selectedLanguage = session('selectedLanguage');
        $step2Page = null;
        $niceNames = [];
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('image_size_error_message')->first();
            $step2Page = Step2PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'image' => isset($step2Page->photo_error) ? $step2Page->photo_error : '',
            ];
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('image_size_error_message')->first();
            $step2Page = Step2PageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'image' => isset($step2Page->photo_error) ? $step2Page->photo_error : '',
            ];
        }

        $user = auth()->user();

        $customMessages = [
            'max' => $message->image_size_error_message,
        ];

        // Manual validation for file extensions if file is uploaded (to avoid requiring php_fileinfo extension)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
            
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors(['image' => 'The image must be a file of type: jpeg, png, jpg, gif.'])->withInput();
            }
        }

        // Use extensions instead of mimes to avoid requiring php_fileinfo extension
        $validator = Validator::make($request->all(), [
            'image' => in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png']) ? 'nullable|file|max:10240' : 'nullable',
        ], $customMessages, $niceNames);

        // Check if validation fails
        if ($validator->fails()) {
            // Check if the error is related to file size
            if ($validator->errors()->has('image') && $validator->errors()->first('image') === 'Can not upload image size greater than 10MB') {
                return redirect()->back()->withErrors(['image' => 'Can not upload image size greater than 10MB'])->withInput();
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/users_images');
            $file->move($destination_path, $filename);
            User::whereId($id)->update([
                'profile_image' => $filename,
                'profile_original_image' => $filename
            ]);
        }

        User::whereId($id)->update([
            'step2' => 1
        ]);

        $user = User::whereId($id)->first();
        
        session(['uploaded_profile_image' => $user->profile_image]);

        return redirect()->route('step3to5', ['lang' => $selectedLanguage->abbreviation]);
    }
}
