<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $existingRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('type', 'admin')
            ->first();

        if (!$existingRecord) {
            // If a record with the same email and type does not exists,
            return redirect()->route('admin.login');
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // $niceNames = [];
        // $lang = getDefaultLanguage(true);
        // $reset_password_setting = getI2bModalSetting($lang, ['reset_password_setting']);
        // if ($reset_password_setting) {
        //     App::setLocale($lang->abbreviation);
        //     $niceNames['email'] = isset($reset_password_setting['email_error_text']) ? $reset_password_setting['email_error_text'] : '';
        //     $niceNames['password'] = isset($reset_password_setting['password_error_text']) ? $reset_password_setting['password_error_text'] : '';
        //     $niceNames['password_confirmation'] = isset($reset_password_setting['confirm_password_error_text']) ? $reset_password_setting['confirm_password_error_text'] : '';
        // }
        
        $request->validate([
            'token' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // $general_setting = getGeneralSettingByKey();

        $password_resets = DB::table('password_resets')->where('token', $request->token)->where('type', 'admin')->first();
        if (!$password_resets) {
            return back()->withErrors(['email' => "This password reset token is invalid."]);
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user) use ($request) {
        //         $user->forceFill([
        //             'password' => Hash::make($request->password),
        //             'remember_token' => Str::random(60),
        //         ])->save();

        //         event(new PasswordReset($user));
        //     }
        // );
        $admin = Admin::where('email', $password_resets->email)->first();
        if (!$admin) {
            return back()->withErrors(['email' => "We can't find a user with that email address."]);
        }
        if ($admin) {
            $adminUpdate = $admin->update([
                'password' => Hash::make($request->password)
            ]);
            if ($adminUpdate) {
                DB::table('password_resets')->where('token', $request->token)->delete();
                return redirect()->route('admin.login')->with('status', 'Your password has been reset successfully.');
            }
        }
    }
}
