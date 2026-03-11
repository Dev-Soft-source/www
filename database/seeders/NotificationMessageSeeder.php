<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\NotificationMessage;
use App\Models\NotificationMessageDetail;
use Illuminate\Database\Seeder;

class NotificationMessageSeeder extends Seeder
{
    public function run()
    {
        $english = Language::where('abbreviation', 'en')->first()
            ?: Language::where('is_default', 1)->first();

        if (!$english) {
            return;
        }

        $messages = [
            'holiday_christmas_new_year' => [
                'name' => 'Holiday Christmas / New Year',
                'placeholders' => [],
                'message' => 'Merry Christmas and Happy New Year!',
            ],
            'birthday_best_member' => [
                'name' => 'Birthday Best Member',
                'placeholders' => [],
                'message' => 'Happy Birthday to our BEST member!',
            ],
            'ride_review_prompt' => [
                'name' => 'Ride Review Prompt',
                'placeholders' => [],
                'message' => 'How did your ride go?',
            ],
            'card_removed_from_profile' => [
                'name' => 'Card Removed',
                'placeholders' => [],
                'message' => 'Card removed from your profile',
            ],
            'welcome_to_proximaride' => [
                'name' => 'Welcome Notification',
                'placeholders' => [],
                'message' => 'Welcome to ProximaRide',
            ],
            'chat_new_message_received' => [
                'name' => 'Chat New Message',
                'placeholders' => ['first_name'],
                'message' => 'New message received from {first_name}',
            ],
            'email_added_to_profile' => [
                'name' => 'Email Added',
                'placeholders' => [],
                'message' => 'A new email address added to your profile',
            ],
            'password_changed' => [
                'name' => 'Password Changed',
                'placeholders' => [],
                'message' => 'Your password has just been changed',
            ],
            'phone_added_to_profile' => [
                'name' => 'Phone Added',
                'placeholders' => [],
                'message' => 'A new phone number added to your profile',
            ],
            'phone_removed_from_profile' => [
                'name' => 'Phone Removed',
                'placeholders' => [],
                'message' => 'Phone number removed from your profile',
            ],
            'vehicle_added_to_profile' => [
                'name' => 'Vehicle Added',
                'placeholders' => [],
                'message' => 'A new vehicle added to your profile',
            ],
            'vehicle_removed_from_profile' => [
                'name' => 'Vehicle Removed',
                'placeholders' => [],
                'message' => 'Vehicle removed from your profile',
            ],
            'student_card_added_to_profile' => [
                'name' => 'Student Card Added',
                'placeholders' => [],
                'message' => 'A new student card added to your profile',
            ],
            'student_card_approved' => [
                'name' => 'Student Card Approved',
                'placeholders' => [],
                'message' => 'Student card approved',
            ],
            'eligible_to_post_extra_plus_rides' => [
                'name' => 'Eligible To Post Extra+',
                'placeholders' => [],
                'message' => 'You are now eligible to post Extra+ Rides',
            ],
            'booking_request_expired' => [
                'name' => 'Booking Request Expired',
                'placeholders' => [],
                'message' => 'Booking request expired',
            ],
            'booking_request_new' => [
                'name' => 'Booking Request New',
                'placeholders' => ['first_name', 'seats'],
                'message' => "You have a new booking request from {first_name}\nSeats booked: {seats}",
            ],
            'booking_request_from_name' => [
                'name' => 'Booking Request From Name',
                'placeholders' => ['first_name'],
                'message' => 'Booking request from {first_name}',
            ],
            'booking_request_approved_by' => [
                'name' => 'Booking Request Approved By',
                'placeholders' => ['first_name'],
                'message' => 'Booking request approved by {first_name}',
            ],
            'booking_approved_you_have_approved' => [
                'name' => 'Booking Approved You Have Approved',
                'placeholders' => ['first_name', 'seats'],
                'message' => "You have approved {first_name}\nSeats booked: {seats}",
            ],
            'secured_cash_payment_code' => [
                'name' => 'Secured Cash Payment Code',
                'placeholders' => ['code'],
                'message' => 'Your Secured-cash payment code is: {code}',
            ],
            'booking_request_declined' => [
                'name' => 'Booking Request Declined',
                'placeholders' => [],
                'message' => 'Booking request declined',
            ],
            'instant_booking_new' => [
                'name' => 'Instant Booking New',
                'placeholders' => ['first_name', 'seats'],
                'message' => "You have a new instant booking from {first_name}\nSeats booked: {seats}",
            ],
            'booking_details_with_seats' => [
                'name' => 'Booking Details With Seats',
                'placeholders' => ['seats'],
                'message' => "Your booking details\nSeats booked: {seats}",
            ],
            'booking_cancelled' => [
                'name' => 'Booking Cancelled',
                'placeholders' => [],
                'message' => 'Booking cancelled',
            ],
            'driver_cancelled_your_booking' => [
                'name' => 'Driver Cancelled Your Booking',
                'placeholders' => [],
                'message' => 'Driver cancelled your booking',
            ],
            'secured_cash_payment_code_successful' => [
                'name' => 'Secured Cash Payment Code Successful',
                'placeholders' => [],
                'message' => 'Secured-cash payment code successful',
            ],
            'your_ride_has_been_cancelled' => [
                'name' => 'Your Ride Has Been Cancelled',
                'placeholders' => [],
                'message' => 'Your ride has been cancelled',
            ],
            'passenger_list_updated' => [
                'name' => 'Passenger List Updated',
                'placeholders' => [],
                'message' => 'Your passenger list has been updated',
            ],
            'seats_booked_count' => [
                'name' => 'Seats Booked Count',
                'placeholders' => ['seats'],
                'message' => '{seats} seats booked',
            ],
            'booking_success_count' => [
                'name' => 'Booking Success Count',
                'placeholders' => ['seats'],
                'message' => '{seats} booked successfully',
            ],
            'seats_needed_count' => [
                'name' => 'Seats Needed Count',
                'placeholders' => ['seats'],
                'message' => '{seats} seats needed',
            ],
            'ride_live_standard' => [
                'name' => 'Ride Live Standard',
                'placeholders' => [],
                'message' => 'Your ride is now live on ProximaRide',
            ],
            'ride_live_requires_vehicle' => [
                'name' => 'Ride Live Requires Vehicle',
                'placeholders' => [],
                'message' => 'Add your vehicle to make your ride live',
            ],
            'ride_live_pink' => [
                'name' => 'Ride Live Pink',
                'placeholders' => [],
                'message' => 'Your Pink Ride is now live on ProximaRide',
            ],
            'ride_live_extra_care' => [
                'name' => 'Ride Live Extra Care',
                'placeholders' => [],
                'message' => 'Your Extra+ Ride is now live on ProximaRide',
            ],
            'ride_live_pink_extra_care' => [
                'name' => 'Ride Live Pink Extra Care',
                'placeholders' => [],
                'message' => 'Your Pink and Extra+ ride is now live on ProximaRide',
            ],
        ];

        foreach ($messages as $slug => $config) {
            $message = NotificationMessage::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $config['name'],
                    'placeholders' => $config['placeholders'],
                ]
            );

            NotificationMessageDetail::updateOrCreate(
                [
                    'notification_message_id' => $message->id,
                    'language_id' => $english->id,
                ],
                [
                    'message' => $config['message'],
                ]
            );
        }
    }
}
