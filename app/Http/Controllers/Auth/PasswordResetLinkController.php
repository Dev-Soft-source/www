<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminResetPasswordMail;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        if(!Admin::where('email', $request->email)->exists()){
            return back()->withErrors(['email' => "We can't find a admin with that email address."]);
        }

        $token = Str::random(64);

        $existingRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'admin')
            ->first();

        if ($existingRecord) {
            // If a record with the same email and type exists, delete it
            DB::table('password_resets')
                ->where('email', $request->email)
                ->where('type', 'admin')
                ->delete();
        }
        
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'type' => 'admin',
            'created_at' => Carbon::now()
        ]);


        $data = ['token' => $token, 'email' => $request->email];
        
        Mail::to($request->email)->queue(new AdminResetPasswordMail($data));

        return redirect()->route('admin.login')->with(['status' => "We have Emailed your password reset link!"]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // $status = Password::sendResetLink(
        //     $request->only('email')
        // );

        // return $status == Password::RESET_LINK_SENT
        //             ? back()->with('status', __($status))
        //             : back()->withInput($request->only('email'))
        //                     ->withErrors(['email' => __($status)]);
    }
}
