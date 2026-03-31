<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // Map of page settings to their routes and display names
    private $pageSettingsMap = [
        'billing_address_setting_detail' => [
            'route' => '/admin/pages/billing-address-settings',
            'name' => 'Billing Address Settings'
        ],
        'home_page_setting_detail' => [
            'route' => '/admin/pages/home-page-settings',
            'name' => 'Home Page Settings'
        ],
        'student_page_setting_detail' => [
            'route' => '/admin/pages/student-page-settings',
            'name' => 'Student Page Settings'
        ],
        'driver_page_setting_detail' => [
            'route' => '/admin/pages/driver-page-settings',
            'name' => 'Driver Page Settings'
        ],
        'passenger_page_setting_detail' => [
            'route' => '/admin/pages/passenger-page-settings',
            'name' => 'Passenger Page Settings'
        ],
        'find_ride_page_setting_detail' => [
            'route' => '/admin/pages/find-ride-page-settings',
            'name' => 'Find Ride Page Settings'
        ],
        'ride_detail_page_setting_detail' => [
            'route' => '/admin/pages/ride-details-page-settings',
            'name' => 'Ride Details Page Settings'
        ],
        'booking_page_setting_detail' => [
            'route' => '/admin/pages/booking-page-settings',
            'name' => 'Booking Page Settings'
        ],
        'post_ride_page_setting_detail' => [
            'route' => '/admin/pages/post-ride-page-settings',
            'name' => 'Post Ride Page Settings'
        ],
        'login_page_setting_detail' => [
            'route' => '/admin/pages/login-page-settings',
            'name' => 'Login Page Settings'
        ],
        'profile_page_setting_detail' => [
            'route' => '/admin/pages/profile-page-settings',
            'name' => 'Profile Page Settings'
        ],
        'edit_profile_page_setting_detail' => [
            'route' => '/admin/pages/edit-profile-page-settings',
            'name' => 'Edit Profile Page Settings'
        ],
        'my_wallet_setting_detail' => [
            'route' => '/admin/pages/my-wallet-settings',
            'name' => 'My Wallet Settings'
        ],
        'payment_setting_detail' => [
            'route' => '/admin/pages/payment-settings',
            'name' => 'Payment Settings'
        ],
        'payout_option_setting_detail' => [
            'route' => '/admin/pages/payout-settings',
            'name' => 'Payout Settings'
        ],
        'my_review_setting_detail' => [
            'route' => '/admin/pages/reviews-settings',
            'name' => 'Reviews Settings'
        ],
        'contact_proxima_ride_setting_detail' => [
            'route' => '/admin/pages/contact-proximaride-settings',
            'name' => 'Contact ProximaRide Settings'
        ],
        'close_account_setting_detail' => [
            'route' => '/admin/pages/close-account-settings',
            'name' => 'Close Account Settings'
        ],
        'logout_setting_detail' => [
            'route' => '/admin/pages/logout-settings',
            'name' => 'Logout Settings'
        ],
        'forgot_password_page_setting_detail' => [
            'route' => '/admin/pages/forgot-password-settings',
            'name' => 'Forgot Password Settings'
        ],
        'signup_page_setting_detail' => [
            'route' => '/admin/pages/signup-page-settings',
            'name' => 'Signup Page Settings'
        ],
        'reset_password_page_setting_detail' => [
            'route' => '/admin/pages/reset-password-settings',
            'name' => 'Reset Password Settings'
        ],
        'privacy_policy_page_setting_detail' => [
            'route' => '/admin/pages/privacy-policy-settings',
            'name' => 'Privacy Policy Settings'
        ],
        'terms_and_condition_page_setting_detail' => [
            'route' => '/admin/pages/terms-and-condition-page-settings',
            'name' => 'Terms and Condition Page Settings'
        ],
        'terms_of_use_page_setting_detail' => [
            'route' => '/admin/pages/terms-of-use-page-settings',
            'name' => 'Terms of Use Page Settings'
        ],
        'refund_policy_page_setting_detail' => [
            'route' => '/admin/pages/refund-policy-page-settings',
            'name' => 'Refund Policy Page Settings'
        ],
        'cancellation_page_setting_detail' => [
            'route' => '/admin/pages/cancellation-page-settings',
            'name' => 'Cancellation Page Settings'
        ],
        'dispute_page_setting_detail' => [
            'route' => '/admin/pages/dispute-page-settings',
            'name' => 'Dispute Page Settings'
        ],
        'coffee_wall_page_setting_detail' => [
            'route' => '/admin/pages/coffee-wall-page-settings',
            'name' => 'Coffee Wall Page Settings'
        ],
        'chats_page_setting_detail' => [
            'route' => '/admin/pages/chats-page-settings',
            'name' => 'Chats Page Settings'
        ],
        'trips_page_setting_detail' => [
            'route' => '/admin/pages/my-trips-page-settings',
            'name' => 'Trips Page Settings'
        ],
        'thankyou_page_setting_detail' => [
            'route' => '/admin/pages/thankyou-page-settings',
            'name' => 'Thank You Page Settings'
        ],
        'referral_page_setting_detail' => [
            'route' => '/admin/pages/referral-page-settings',
            'name' => 'Referral Page Settings'
        ],
        'contact_us_page_setting_detail' => [
            'route' => '/admin/pages/contact-us-page-settings',
            'name' => 'Contact Us Page Settings'
        ],
        'cost_sharing_page_setting_detail' => [
            'route' => '/admin/pages/cost-sharing-page-settings',
            'name' => 'Cost Sharing Page Settings'
        ],
        'notifications_page_setting_detail' => [
            'route' => '/admin/pages/notifications-page-settings',
            'name' => 'Notifications Page Settings'
        ],
        'profile_photo_guidelines_page_setting_detail' => [
            'route' => '/admin/pages/profile-photo-guidelines-page-settings',
            'name' => 'Profile Photo Guidelines Page Settings'
        ],
        'success_messages_setting_detail' => [
            'route' => '/admin/success-error-messages-setting',
            'name' => 'Success & Error Messages Settings'
        ],

        // Additional/legacy table names observed in production DBs
        'add_vehicle_setting_detail' => [
            'route' => '/admin/pages/my-vehicle-settings',
            'name' => 'My Vehicle Settings'
        ],
        'edit_vehicle_setting_detail' => [
            'route' => '/admin/pages/my-vehicle-settings',
            'name' => 'My Vehicle Settings'
        ],
        'my_vehicle_setting_detail' => [
            'route' => '/admin/pages/my-vehicle-settings',
            'name' => 'My Vehicle Settings'
        ],
        'my_driver_license_setting_detail' => [
            'route' => '/admin/pages/my-driver-license-settings',
            'name' => 'My Driver License Settings'
        ],
        'my_student_card_setting_detail' => [
            'route' => '/admin/pages/my-student-card-settings',
            'name' => 'My Student Card Settings'
        ],
        'my_phone_no_setting_detail' => [
            'route' => '/admin/pages/my-phone-settings',
            'name' => 'My Phone Settings'
        ],
        'my_email_address_setting_detail' => [
            'route' => '/admin/pages/my-email-settings',
            'name' => 'My Email Settings'
        ],
        'my_passenger_setting_detail' => [
            'route' => '/admin/pages/my-passenger-settings',
            'name' => 'My Passenger Settings'
        ],
        'profile_setting_detail' => [
            'route' => '/admin/pages/profile-settings',
            'name' => 'Profile Settings'
        ],
        'profile_photo_setting_detail' => [
            'route' => '/admin/pages/profile-photo-settings',
            'name' => 'Profile Photo Settings'
        ],
        'password_setting_detail' => [
            'route' => '/admin/pages/password-settings',
            'name' => 'Password Settings'
        ],
        'payment_option_setting_detail' => [
            'route' => '/admin/pages/payment-settings',
            'name' => 'Payment Settings'
        ],
        'error_page_setting_detail' => [
            'route' => '/admin/pages/error-page-settings',
            'name' => 'Error Page Settings'
        ],
        'features_setting_detail' => [
            'route' => '/admin/pages/manage-features',
            'name' => 'Manage Features Settings'
        ],
        'firm_cancellation_setting_detail' => [
            'route' => '/admin/pages/firm-cancellation-page-settings',
            'name' => 'Firm Cancellation Page Settings'
        ],
        'select_location_setting_detail' => [
            'route' => '/admin/pages/location-page-settings',
            'name' => 'Location Page Settings'
        ],
        'media_setting_detail' => [
            'route' => '/admin/pages/media-page-settings',
            'name' => 'Media Page Settings'
        ],
        'top_up_balance_setting_detail' => [
            'route' => '/admin/pages/my-wallet-settings',
            'name' => 'My Wallet Settings'
        ],
        'ride_fair_setting_detail' => [
            'route' => '/admin/pages/ride-details-page-settings',
            'name' => 'Ride Details Page Settings'
        ],
        'close_my_account_setting_detail' => [
            'route' => '/admin/pages/close-account-settings',
            'name' => 'Close Account Settings'
        ],
        'coffee_wall_setting_detail' => [
            'route' => '/admin/pages/coffee-wall-page-settings',
            'name' => 'Coffee Wall Page Settings'
        ],
        'community_guidelines_page_setting_details' => [
            'route' => '/admin/pages/community-guidelines-page-settings',
            'name' => 'Community Guidelines Page Settings'
        ],
        'referral_page_setting_details' => [
            'route' => '/admin/pages/referral-page-settings',
            'name' => 'Referral Page Settings'
        ],
        'reward_point_setting_details' => [
            'route' => '/admin/registration-reward-settings/1/edit',
            'name' => 'Registration Reward Settings'
        ],
        'footer_setting_details' => [
            'route' => '/admin/site-text-setting',
            'name' => 'Site Text Settings'
        ],
        'signup_page_setting_detail_copy1' => [
            'route' => '/admin/pages/signup-page-settings',
            'name' => 'Signup Page Settings'
        ],
        'step1_page_setting_detail' => [
            'route' => '/admin/pages/step1-page-settings',
            'name' => 'Step 1 Page Settings'
        ],
        'step2_page_setting_detail' => [
            'route' => '/admin/pages/step2-page-settings',
            'name' => 'Step 2 Page Settings'
        ],
        'step3_page_setting_detail' => [
            'route' => '/admin/pages/step3-page-settings',
            'name' => 'Step 3 Page Settings'
        ],
        'step4_page_setting_detail' => [
            'route' => '/admin/pages/step4-page-settings',
            'name' => 'Step 4 Page Settings'
        ],
        'step5_page_setting_detail' => [
            'route' => '/admin/pages/step5-page-settings',
            'name' => 'Step 5 Page Settings'
        ],
        'disclaimer_page_setting_detail' => [
            'route' => '/admin/pages/disclaimer-page-settings',
            'name' => 'Disclaimer Page Settings'
        ],
        'for_tourists_page_setting_detail' => [
            'route' => '/admin/pages/for-tourists-page-settings',
            'name' => 'For Tourists Page Settings'
        ],
    ];

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        $results = [];
        $searchTerm = '%' . $query . '%';

        // Search across all page setting detail tables
        foreach ($this->pageSettingsMap as $tableName => $pageInfo) {
            try {
                // Check if table exists
                if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                    continue;
                }

                // Get all text columns from the table
                $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
                
                // Filter to only text/varchar columns (exclude id, foreign keys, timestamps)
                $textColumns = array_filter($columns, function($col) {
                    return !in_array($col, ['id', 'language_id', 'created_at', 'updated_at']) 
                        && !str_ends_with($col, '_id');
                });

                if (empty($textColumns)) {
                    continue;
                }

                // Build WHERE clause to search across all text columns (case-insensitive)
                $whereConditions = [];
                $bindings = [];
                foreach ($textColumns as $column) {
                    $whereConditions[] = "LOWER(`{$column}`) LIKE LOWER(?)";
                    $bindings[] = $searchTerm;
                }
                $whereClause = '(' . implode(' OR ', $whereConditions) . ')';

                // Execute search query
                $matches = DB::table($tableName)
                    ->whereRaw($whereClause, $bindings)
                    ->select('id', 'language_id')
                    ->distinct()
                    ->get();

                if ($matches->count() > 0) {
                    // Get the first match to show preview text
                    $firstMatch = DB::table($tableName)
                        ->whereRaw($whereClause, $bindings)
                        ->first();

                    // Find which column contains the match for preview
                    $previewText = '';
                    $queryLower = strtolower($query);
                    foreach ($textColumns as $column) {
                        $columnValue = $firstMatch->$column ?? null;
                        if ($columnValue && stripos($columnValue, $query) !== false) {
                            // Highlight the search term in preview
                            $previewText = mb_substr($columnValue, 0, 150);
                            if (mb_strlen($columnValue) > 150) {
                                $previewText .= '...';
                            }
                            break;
                        }
                    }
                    
                    // If no preview found, use first non-empty column
                    if (empty($previewText)) {
                        foreach ($textColumns as $column) {
                            $columnValue = $firstMatch->$column ?? null;
                            if (!empty($columnValue)) {
                                $previewText = mb_substr($columnValue, 0, 150);
                                if (mb_strlen($columnValue) > 150) {
                                    $previewText .= '...';
                                }
                                break;
                            }
                        }
                    }

                    $results[] = [
                        'page_name' => $pageInfo['name'],
                        'route' => $pageInfo['route'],
                        'preview' => $previewText,
                        'match_count' => $matches->count()
                    ];
                }
            } catch (\Exception $e) {
                // Skip tables that don't exist or have errors
                continue;
            }
        }

        // Remove duplicates and sort by match count
        $uniqueResults = [];
        foreach ($results as $result) {
            $key = $result['route'];
            if (!isset($uniqueResults[$key])) {
                $uniqueResults[$key] = $result;
            } else {
                // If duplicate, keep the one with more matches
                if ($result['match_count'] > $uniqueResults[$key]['match_count']) {
                    $uniqueResults[$key] = $result;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => array_values($uniqueResults)
        ]);
    }
}
