<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Verify email token validity
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken($token)
    {
        try {
            // Check if token exists and is for email verification
            $verificationRecord = DB::table('password_resets')
                ->where('token', $token)
                ->where('type', 'verify_email')
                ->first();

            if (!$verificationRecord) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid verification token'
                ], 404);
            }

            // Check if token is expired (24 hours)
            $tokenAge = Carbon::now()->diffInHours(Carbon::parse($verificationRecord->created_at));
            
            if ($tokenAge > 24) {
                // Clean up expired token
                DB::table('password_resets')
                    ->where('token', $token)
                    ->where('type', 'verify_email')
                    ->delete();

                return response()->json([
                    'valid' => false,
                    'message' => 'Verification token has expired'
                ], 410);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Token is valid',
                'email' => $verificationRecord->email
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error validating token'
            ], 500);
        }
    }
}