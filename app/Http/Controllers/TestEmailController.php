<?php

namespace App\Http\Controllers;

use App\Mail\UserEmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestEmailController extends Controller
{
    /**
     * Send test email verification email
     */
    public function sendTestEmailVerification(Request $request)
    {
        $testEmail = 'muhabd0336@gmail.com';
        $token = Str::random(64);
        
        // Determine if this is for app testing
        $isApp = $request->has('app') && $request->get('app') === 'true';
        
        $data = [
            'first_name' => 'Test User',
            'email' => $testEmail,
            'token' => $token,
            'app' => $isApp
        ];
        
        try {
            Mail::to($testEmail)->send(new UserEmailVerification($data));
            
            $type = $isApp ? 'App' : 'Web';
            $message = "{$type} email verification test sent successfully to {$testEmail}";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'email' => $testEmail,
                    'token' => $token,
                    'app' => $isApp,
                    'verification_url' => route('emailVerify', [
                        'token' => $token, 
                        'email' => $testEmail
                    ]) . ($isApp ? '?app=true' : '')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
}