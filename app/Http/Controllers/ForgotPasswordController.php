<?php

namespace App\Http\Controllers;

use App\Mail\UserForgotPassword;
use App\Models\ForgotPasswordPageSettingDetail;
use App\Models\Language;
use App\Models\LoginPageSettingDetail;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function create($lang = null){
        
        $forgotPasswordPage = ForgotPasswordPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        return view('forgot_password',['forgotPasswordPage' => $forgotPasswordPage]);
    }

    public function store(Request $request) {
        
        $messages = $this->successMessage;

        $forgotPasswordPage = ForgotPasswordPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $loginPage = LoginPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $niceNames = [
            'email' => isset($forgotPasswordPage) && isset($forgotPasswordPage->email_error) ? $forgotPasswordPage->email_error : '',
        ];

        // Validate the form data with AJAX support
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
            ], [], $niceNames);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $errorMsg = isset($messages) && isset($messages->no_user_found_message) ? $messages->no_user_found_message : 'No user found with this email address.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['email' => [$errorMsg]],
                ], 422);
            }
            
            return back()->withErrors(['email' => $errorMsg]);
        }

        // Check if the user's account is closed
        if ($user->closed === '1') {
            // Pass the user to the session so it's available in the view
            session(['user' => $user]);

            // Get the close_modal_error_message from LoginPageSettingDetail
            $closeModalErrorMessage = isset($loginPage) && isset($loginPage->close_modal_error_message) ? $loginPage->close_modal_error_message : 'This account has been closed';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $closeModalErrorMessage,
                ], 422);
            }
            
            return back()->with(['error' => $closeModalErrorMessage])->withInput();
        }

        // If the account is not closed and email is verified
        if ($user->email_verified == 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'   => false,
                    'showModal' => true,
                    'user'      => ['email' => $user->email],
                    'message'   => 'This email isn\'t verified yet.',
                ], 422);
            }
            
            return back()->with(['showModal' => true, 'user' => $user]);
        }

        $token = Str::random(64);

        $existingRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'user')
            ->first();

        if ($existingRecord) {
            // If a record with the same email and type exists, delete it
            DB::table('password_resets')
                ->where('email', $request->email)
                ->where('type', 'user')
                ->delete();
        }

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'type' => 'user',
            'created_at' => Carbon::now()
        ]);

        $data = ['token' => $token, 'first_name' => $user->first_name, 'lang' => $this->selectedLanguage->abbreviation];

        // Send reset password mail
        try {
            Mail::to($request->email)->queue(new UserForgotPassword($data));
        } catch (\Exception $e) {
            \Log::error('Forgot password email failed: ' . $e->getMessage());
            $errorMsg = isset($forgotPasswordPage) && isset($forgotPasswordPage->fail_send) ? $forgotPasswordPage->fail_send : 'Failed to send reset password email.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['email' => [$errorMsg]],
                ], 422);
            }
            
            return back()->withErrors(['email' => $errorMsg]);
        }

        $successMessage = isset($messages) && isset($messages->reset_password_message) ? $messages->reset_password_message : 'Password reset email has been sent successfully.';
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        return redirect()->route('forgot.password', ['lang' => $this->selectedLanguage->abbreviation])
                        ->with(['message' => $successMessage]);
    }
}
