<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class AccessPortalController extends Controller
{
    public function index($email){
        $user = User::where('email', $email)->first();
        
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        if ($user) {
            $user = auth()->login($user);    
            return redirect()->route('profile', ['lang' => $selectedLanguage->abbreviation]);
        } else {
            return redirect()->route('login', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Account is not available anymore"]);
        }
    }
}
