<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfilePhotoSettingDetail;
use App\Models\Step2PageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\User;
use Illuminate\Http\Request;

class ProfilePhotoController extends Controller
{
    public function index($lang = null){
        
        $profilePhotoPage = ProfilePhotoSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $step2Page = Step2PageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();

            return view('profile_photo',['profilePhotoPage' => $profilePhotoPage,'reviewSetting' => $reviewSetting,
            'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'user' => $user,
            'step2Page' => $step2Page]);
        } else {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation]);
        }
    }

    public function update($id, Request $request){
        $selectedLanguage = session('selectedLanguage');
        $profilePhotoPage = null;
        $step2Page = null;
        $niceNames = [];
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $profilePhotoPage = ProfilePhotoSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'image' => isset($profilePhotoPage->photo_error) ? $profilePhotoPage->photo_error : '',
            ];
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $profilePhotoPage = ProfilePhotoSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'image' => isset($profilePhotoPage->photo_error) ? $profilePhotoPage->photo_error : '',
            ];
        }

        // Validate file exists and size
        $request->validate([
            'image' => 'required|file|max:10240',
        ], [], $niceNames);
        
        // Manual validation for file extension (works without fileinfo extension)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png'];
            
            if (!in_array($extension, $allowedExtensions)) {
                $errorMessage = isset($niceNames['image']) && !empty($niceNames['image']) 
                    ? $niceNames['image'] 
                    : 'The image must be a file of type: jpeg, jpg, png.';
                    
                return redirect()->back()
                    ->withErrors(['image' => $errorMessage])
                    ->withInput();
            }
            
            // Additional check: verify it's actually an image by checking file signature
            $filePath = $file->getRealPath();
            $fileSignature = file_exists($filePath) ? bin2hex(file_get_contents($filePath, false, null, 0, 4)) : '';
            
            // JPEG: FF D8 FF E0 or FF D8 FF E1 or FF D8 FF DB
            // PNG: 89 50 4E 47
            $isValidImage = (
                strpos($fileSignature, 'ffd8ff') === 0 || // JPEG
                strpos($fileSignature, '89504e47') === 0 // PNG
            );
            
            if (!$isValidImage) {
                $errorMessage = isset($niceNames['image']) && !empty($niceNames['image']) 
                    ? $niceNames['image'] 
                    : 'The uploaded file is not a valid image. Please upload a JPEG or PNG image.';
                    
                return redirect()->back()
                    ->withErrors(['image' => $errorMessage])
                    ->withInput();
            }
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/users_images');
            $file->move($destination_path,$filename);
            User::whereId($id)->update([
                'profile_image' => $filename,
                'profile_original_image' => $filename
            ]);
        }

        return redirect()->route('profile.photo', ['lang' => $selectedLanguage->abbreviation])->with('message', 'Photo updated successfully');
    }
}