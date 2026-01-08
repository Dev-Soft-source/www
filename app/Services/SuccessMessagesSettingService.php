<?php

namespace App\Services;

use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Support\Facades\Log;

class SuccessMessagesSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['profile_update_message.profile_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['profile_update_message.profile_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_removed_message.vehicle_removed_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_removed_message.vehicle_removed_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_update_message.vehicle_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_update_message.vehicle_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_add_message.vehicle_add_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_add_message.vehicle_add_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_update_message.password_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_update_message.password_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_delete_message.phone_delete_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_delete_message.phone_delete_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_add_message.phone_add_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_add_message.phone_add_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_verified_message.phone_verified_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_verified_message.phone_verified_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['license_upload_message.license_upload_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['license_upload_message.license_upload_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['student_card_upload_message.student_card_upload_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['student_card_upload_message.student_card_upload_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_add_message.card_add_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_add_message.card_add_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_primary_message.card_primary_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_primary_message.card_primary_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_delete_message.card_delete_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_delete_message.card_delete_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_cancel_message.ride_cancel_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_cancel_message.ride_cancel_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_save_message.bank_save_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_save_message.bank_save_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_update_message.paypal_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_update_message.paypal_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['paypal_saved_message.paypal_saved_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_saved_message.paypal_saved_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['replied_message.replied_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['replied_message.replied_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_post_message.ride_post_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_post_message.ride_post_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['book_seat_message.book_seat_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['book_seat_message.book_seat_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['book_seat_messagebook_seat_message_end_part.book_seat_messagebook_seat_message_end_part_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['book_seat_messagebook_seat_message_end_part.book_seat_messagebook_seat_message_end_part_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_verified_message.email_verified_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_verified_message.email_verified_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['continue_with_app_btn_label.continue_with_app_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['continue_with_app_btn_label.continue_with_app_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['create_my_profile_btn_label.create_my_profile_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['create_my_profile_btn_label.create_my_profile_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['profile_photo_update_message.profile_photo_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['profile_photo_update_message.profile_photo_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['welcome_message.welcome_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['welcome_message.welcome_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_sent_message.email_sent_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_sent_message.email_sent_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['hey_message.hey_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['hey_message.hey_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['complete_profile_message.complete_profile_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['complete_profile_message.complete_profile_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_user_found_message.no_user_found_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_user_found_message.no_user_found_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_user_match_message.no_user_match_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_user_match_message.no_user_match_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['image_size_error_message.image_size_error_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['image_size_error_message.image_size_error_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['incorrect_password_message.incorrect_password_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['incorrect_password_message.incorrect_password_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['incorrect_code_message.incorrect_code_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['incorrect_code_message.incorrect_code_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['phone_code_error_message.phone_code_error_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_code_error_message.phone_code_error_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['already_added_card_message.already_added_card_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['already_added_card_message.already_added_card_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_schedule_message.ride_schedule_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_schedule_message.ride_schedule_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['female_user_message.female_user_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['female_user_message.female_user_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['star5_passenger_message.star5_passenger_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['star5_passenger_message.star5_passenger_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['star4_passenger_message.star4_passenger_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['star4_passenger_message.star4_passenger_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['star3_passenger_message.star3_passenger_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['star3_passenger_message.star3_passenger_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_with_review_message.passenger_with_review_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_with_review_message.passenger_with_review_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['past_time_message.past_time_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['past_time_message.past_time_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['past_date_message.past_date_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['past_date_message.past_date_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['enter_code_message.enter_code_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['enter_code_message.enter_code_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['url_not_allowed_message.url_not_allowed_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['url_not_allowed_message.url_not_allowed_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_not_allowed_message.email_not_allowed_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_not_allowed_message.email_not_allowed_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_number_not_allowed_message.phone_number_not_allowed_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_number_not_allowed_message.phone_number_not_allowed_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['need_to_select_card_message.need_to_select_card_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['need_to_select_card_message.need_to_select_card_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_not_completed_message.paypal_not_completed_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_not_completed_message.paypal_not_completed_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_result_clear_message.search_result_clear_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_result_clear_message.search_result_clear_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['delete_card_message.delete_card_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_card_message.delete_card_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['withdraw_message.withdraw_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['withdraw_message.withdraw_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['delete_vehicle_message.delete_vehicle_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_vehicle_message.delete_vehicle_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['remove_driver_license_message.remove_driver_license_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['remove_driver_license_message.remove_driver_license_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['popup_close_btn_text.popup_close_btn_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['popup_close_btn_text.popup_close_btn_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_reason.select_reason_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_reason.select_reason_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_recommend.select_recommend_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_recommend.select_recommend_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['check_box.check_box_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['check_box.check_box_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reviewed_driver_message.reviewed_driver_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reviewed_driver_message.reviewed_driver_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reviewed_passenger_message.reviewed_passenger_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reviewed_passenger_message.reviewed_passenger_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reject_booking_message.reject_booking_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reject_booking_message.reject_booking_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_booking_message.cancel_booking_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_message.cancel_booking_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['contact_form_message.contact_form_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['contact_form_message.contact_form_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['acc_suspend_message.acc_suspend_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['acc_suspend_message.acc_suspend_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_unavailable_message.seat_unavailable_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_unavailable_message.seat_unavailable_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verified_number_message.verified_number_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verified_number_message.verified_number_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_hold_message.seat_hold_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_hold_message.seat_hold_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verified_email_message.verified_email_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verified_email_message.verified_email_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_not_verify_message.email_not_verify_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_not_verify_message.email_not_verify_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reset_password_message.reset_password_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reset_password_message.reset_password_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_update_message.email_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_update_message.email_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
    
                $validationRule = array_merge($validationRule, ['general_error_message.general_error_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['general_error_message.general_error_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['current_email_not_match.current_email_not_match_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['current_email_not_match.current_email_not_match_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_not_found_message.email_not_found_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_not_found_message.email_not_found_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_reset_success_message.password_reset_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_reset_success_message.password_reset_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['overlap_ride_message.overlap_ride_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['overlap_ride_message.overlap_ride_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
    
                $validationRule = array_merge($validationRule, ['topup_balance_success_message.topup_balance_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['topup_balance_success_message.topup_balance_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_expiry_message.card_expiry_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_expiry_message.card_expiry_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verify_amount_not_match_message.verify_amount_not_match_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verify_amount_not_match_message.verify_amount_not_match_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_verified_message.bank_verified_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_verified_message.bank_verified_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_already_verified_message.bank_already_verified_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_already_verified_message.bank_already_verified_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['admin_sent_verify_amount_message.admin_sent_verify_amount_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['admin_sent_verify_amount_message.admin_sent_verify_amount_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_already_exist_message.email_already_exist_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_already_exist_message.email_already_exist_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['request_expired_message.request_expired_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['request_expired_message.request_expired_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['request_accept_message.request_accept_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['request_accept_message.request_accept_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_booked_message.seat_booked_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_booked_message.seat_booked_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_hold_success_message.seat_hold_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_hold_success_message.seat_hold_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['account_closed_message.account_closed_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['account_closed_message.account_closed_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_detail_update_message.bank_detail_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_detail_update_message.bank_detail_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_not_update_message.booking_not_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_not_update_message.booking_not_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['closed_account_success_message.closed_account_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['closed_account_success_message.closed_account_success_message' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reply_already_exist_message.reply_already_exist_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reply_already_exist_message.reply_already_exist_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['post_ride_update_message.post_ride_update_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['post_ride_update_message.post_ride_update_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['license_delete_message.license_delete_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['license_delete_message.license_delete_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['block_booking_message.block_booking_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['block_booking_message.block_booking_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['block_review_rating_message.block_review_rating_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['block_review_rating_message.block_review_rating_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['block_post_ride_message.block_post_ride_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['block_post_ride_message.block_post_ride_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['profile_photo_required_message.profile_photo_required_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['profile_photo_required_message.profile_photo_required_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['password_token_invalid_message.password_token_invalid_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_token_invalid_message.password_token_invalid_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_set_default_message.phone_set_default_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_set_default_message.phone_set_default_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verfification_code_sent_message.verfification_code_sent_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verfification_code_sent_message.verfification_code_sent_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['removed_passenger_message.removed_passenger_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['removed_passenger_message.removed_passenger_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
    
                $validationRule = array_merge($validationRule, ['popup_submit_btn_text.popup_submit_btn_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['popup_submit_btn_text.popup_submit_btn_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['reward_not_found_message.reward_not_found_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reward_not_found_message.reward_not_found_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['claim_reward_student_success_message.claim_reward_student_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['claim_reward_student_success_message.claim_reward_student_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['claim_reward_driver_success_message.claim_reward_driver_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['claim_reward_driver_success_message.claim_reward_driver_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['coffee_wall_heading_success_message.coffee_wall_heading_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['coffee_wall_heading_success_message.coffee_wall_heading_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['coffee_wall_text_success_message.coffee_wall_text_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['coffee_wall_text_success_message.coffee_wall_text_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['too_many_secured_cash_attempt_message.too_many_secured_cash_attempt_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['too_many_secured_cash_attempt_message.too_many_secured_cash_attempt_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['secured_cash_success_message.secured_cash_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['secured_cash_success_message.secured_cash_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);


                $validationRule = array_merge($validationRule, ['not_allowed_post_ride_state_wise_message.not_allowed_post_ride_state_wise_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['not_allowed_post_ride_state_wise_message.not_allowed_post_ride_state_wise_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['admin_block_account_message.admin_block_account_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['admin_block_account_message.admin_block_account_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['add_your_phone.add_your_phone_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['add_your_phone.add_your_phone_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);  

                $validationRule = array_merge($validationRule, ['confirm_cancel_noshow.confirm_cancel_noshow_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirm_cancel_noshow.confirm_cancel_noshow_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);  

                $validationRule = array_merge($validationRule, ['cancel_noshow_take_me_back.cancel_noshow_take_me_back_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_noshow_take_me_back.cancel_noshow_take_me_back_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);  

                $validationRule = array_merge($validationRule, ['cancel_noshow_are_you_sure.cancel_noshow_are_you_sure_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_noshow_are_you_sure.cancel_noshow_are_you_sure_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['popup_signup_btn_text.popup_signup_btn_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['popup_signup_btn_text.popup_signup_btn_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                         
                $validationRule = array_merge($validationRule, ['arbitration_success_message.arbitration_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['arbitration_success_message.arbitration_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['ride_dead_time_text.ride_dead_time_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_dead_time_text.ride_dead_time_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payout_request_success_message.payout_request_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payout_request_success_message.payout_request_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['suspended_account_phone_number_message.suspended_account_phone_number_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['suspended_account_phone_number_message.suspended_account_phone_number_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_request_success_message.booking_request_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_request_success_message.booking_request_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['login_before_booking_message.login_before_booking_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['login_before_booking_message.login_before_booking_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['popup_login_btn_text.popup_login_btn_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['popup_login_btn_text.popup_login_btn_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['no_show_driver_button.no_show_driver_button_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_show_driver_button.no_show_driver_button_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

               
                $validationRule = array_merge($validationRule, ['revert_arbitration_button.revert_arbitration_button_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['revert_arbitration_button.revert_arbitration_button_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                               
                $validationRule = array_merge($validationRule, ['cancel_button.cancel_button_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_button.cancel_button_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['delete_button.delete_button_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_button.delete_button_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($successMessagesSetting, $language, $request)
    {
        Log::info($successMessagesSetting);
        Log::info($successMessagesSetting->id);
        return [
            'messages_setting_id' => $successMessagesSetting->id,
            'language_id' => $language->id,
            'profile_update_message' => $this->data($request, $language, 'profile_update_message'),
            'vehicle_removed_message' => $this->data($request, $language, 'vehicle_removed_message'),
            'vehicle_update_message' => $this->data($request, $language, 'vehicle_update_message'),
            'vehicle_add_message' => $this->data($request, $language, 'vehicle_add_message'),
            'password_update_message' => $this->data($request, $language, 'password_update_message'),
            'phone_delete_message' => $this->data($request, $language, 'phone_delete_message'),
            'phone_add_message' => $this->data($request, $language, 'phone_add_message'),
            'phone_verified_message' => $this->data($request, $language, 'phone_verified_message'),
            'license_upload_message' => $this->data($request, $language, 'license_upload_message'),
            'student_card_upload_message' => $this->data($request, $language, 'student_card_upload_message'),
            'card_add_message' => $this->data($request, $language, 'card_add_message'),
            'card_primary_message' => $this->data($request, $language, 'card_primary_message'),
            'card_delete_message' => $this->data($request, $language, 'card_delete_message'),
            'ride_cancel_message' => $this->data($request, $language, 'ride_cancel_message'),
            'bank_save_message' => $this->data($request, $language, 'bank_save_message'),
            'paypal_update_message' => $this->data($request, $language, 'paypal_update_message'),
            'paypal_saved_message' => $this->data($request, $language, 'paypal_saved_message'),
            'replied_message' => $this->data($request, $language, 'replied_message'),
            'ride_post_message' => $this->data($request, $language, 'ride_post_message'),
            'book_seat_message' => $this->data($request, $language, 'book_seat_message'),
            'book_seat_message_end_part' => $this->data($request, $language, 'book_seat_message_end_part'),
            'email_verified_message' => $this->data($request, $language, 'email_verified_message'),
            'continue_with_app_btn_label' => $this->data($request, $language, 'continue_with_app_btn_label'),
            'create_my_profile_btn_label' => $this->data($request, $language, 'create_my_profile_btn_label'),
            'profile_photo_update_message' => $this->data($request, $language, 'profile_photo_update_message'),
            'welcome_message' => $this->data($request, $language, 'welcome_message'),
            'email_sent_message' => $this->data($request, $language, 'email_sent_message'),
            'hey_message' => $this->data($request, $language, 'hey_message'),
            'complete_profile_message' => $this->data($request, $language, 'complete_profile_message'),
            'no_user_found_message' => $this->data($request, $language, 'no_user_found_message'),
            'no_user_match_message' => $this->data($request, $language, 'no_user_match_message'),
            'image_size_error_message' => $this->data($request, $language, 'image_size_error_message'),
            'incorrect_password_message' => $this->data($request, $language, 'incorrect_password_message'),
            'incorrect_code_message' => $this->data($request, $language, 'incorrect_code_message'),
            'phone_code_error_message' => $this->data($request, $language, 'phone_code_error_message'),
            'already_added_card_message' => $this->data($request, $language, 'already_added_card_message'),
            'ride_schedule_message' => $this->data($request, $language, 'ride_schedule_message'),
            'female_user_message' => $this->data($request, $language, 'female_user_message'),
            'star5_passenger_message' => $this->data($request, $language, 'star5_passenger_message'),
            'star4_passenger_message' => $this->data($request, $language, 'star4_passenger_message'),
            'star3_passenger_message' => $this->data($request, $language, 'star3_passenger_message'),
            'passenger_with_review_message' => $this->data($request, $language, 'passenger_with_review_message'),
            'past_time_message' => $this->data($request, $language, 'past_time_message'),
            'past_date_message' => $this->data($request, $language, 'past_date_message'),
            'enter_code_message' => $this->data($request, $language, 'enter_code_message'),
            'url_not_allowed_message' => $this->data($request, $language, 'url_not_allowed_message'),
            'email_not_allowed_message' => $this->data($request, $language, 'email_not_allowed_message'),
            'phone_number_not_allowed_message' => $this->data($request, $language, 'phone_number_not_allowed_message'),
            'need_to_select_card_message' => $this->data($request, $language, 'need_to_select_card_message'),
            'paypal_not_completed_message' => $this->data($request, $language, 'paypal_not_completed_message'),
            'search_result_clear_message' => $this->data($request, $language, 'search_result_clear_message'),
            'delete_card_message' => $this->data($request, $language, 'delete_card_message'),
            'withdraw_message' => $this->data($request, $language, 'withdraw_message'),
            'delete_vehicle_message' => $this->data($request, $language, 'delete_vehicle_message'),
            'remove_driver_license_message' => $this->data($request, $language, 'remove_driver_license_message'),
            'popup_close_btn_text' => $this->data($request, $language, 'popup_close_btn_text'),
            'select_reason' => $this->data($request, $language, 'select_reason'),
            'select_recommend' => $this->data($request, $language, 'select_recommend'),
            'check_box' => $this->data($request, $language, 'check_box'),
            'reviewed_driver_message' => $this->data($request, $language, 'reviewed_driver_message'),
            'reviewed_passenger_message' => $this->data($request, $language, 'reviewed_passenger_message'),
            'reject_booking_message' => $this->data($request, $language, 'reject_booking_message'),
            'cancel_booking_message' => $this->data($request, $language, 'cancel_booking_message'),
            'contact_form_message' => $this->data($request, $language, 'contact_form_message'),
            'acc_suspend_message' => $this->data($request, $language, 'acc_suspend_message'),
            'seat_unavailable_message' => $this->data($request, $language, 'seat_unavailable_message'),
            'verified_number_message' => $this->data($request, $language, 'verified_number_message'),
            'seat_hold_message' => $this->data($request, $language, 'seat_hold_message'),
            'verified_email_message' => $this->data($request, $language, 'verified_email_message'),
            'email_not_verify_message' => $this->data($request, $language, 'email_not_verify_message'),
            'reset_password_message' => $this->data($request, $language, 'reset_password_message'),
            'email_update_message' => $this->data($request, $language, 'email_update_message'),
            'general_error_message' => $this->data($request, $language, 'general_error_message'),
            'current_email_not_match' => $this->data($request, $language, 'current_email_not_match'),
            'email_not_found_message' => $this->data($request, $language, 'email_not_found_message'),
            'password_reset_success_message' => $this->data($request, $language, 'password_reset_success_message'),
            'overlap_ride_message' => $this->data($request, $language, 'overlap_ride_message'),
            'topup_balance_success_message' => $this->data($request, $language, 'topup_balance_success_message'),
            'card_expiry_message' => $this->data($request, $language, 'card_expiry_message'),
            'verify_amount_not_match_message' => $this->data($request, $language, 'verify_amount_not_match_message'),
            'bank_verified_message' => $this->data($request, $language, 'bank_verified_message'),
            'bank_already_verified_message' => $this->data($request, $language, 'bank_already_verified_message'),
            'admin_sent_verify_amount_message' => $this->data($request, $language, 'admin_sent_verify_amount_message'),
            'email_already_exist_message' => $this->data($request, $language, 'email_already_exist_message'),
            'request_expired_message' => $this->data($request, $language, 'request_expired_message'),
            'request_accept_message' => $this->data($request, $language, 'request_accept_message'),
            'seat_booked_message' => $this->data($request, $language, 'seat_booked_message'),
            'seat_hold_success_message' => $this->data($request, $language, 'seat_hold_success_message'),
            'account_closed_message' => $this->data($request, $language, 'account_closed_message'),
            'bank_detail_update_message' => $this->data($request, $language, 'bank_detail_update_message'),
            'booking_not_update_message' => $this->data($request, $language, 'booking_not_update_message'),
            'closed_account_success_message' => $this->data($request, $language, 'closed_account_success_message'),
            'reply_already_exist_message' => $this->data($request, $language, 'reply_already_exist_message'),
            'post_ride_update_message' => $this->data($request, $language, 'post_ride_update_message'),
            'license_delete_message' => $this->data($request, $language, 'license_delete_message'),
            'block_booking_message' => $this->data($request, $language, 'block_booking_message'),
            'block_review_rating_message' => $this->data($request, $language, 'block_review_rating_message'),
            'block_post_ride_message' => $this->data($request, $language, 'block_post_ride_message'),

            'profile_photo_required_message' => $this->data($request, $language, 'profile_photo_required_message'),

            'password_token_invalid_message' => $this->data($request, $language, 'password_token_invalid_message'),
            'phone_set_default_message' => $this->data($request, $language, 'phone_set_default_message'),
            'verfification_code_sent_message' => $this->data($request, $language, 'verfification_code_sent_message'),
            'removed_passenger_message' => $this->data($request, $language, 'removed_passenger_message'),
            'popup_submit_btn_text' => $this->data($request, $language, 'popup_submit_btn_text'),
            'reward_not_found_message' => $this->data($request, $language, 'reward_not_found_message'),
            'claim_reward_student_success_message' => $this->data($request, $language, 'claim_reward_student_success_message'),
            'claim_reward_driver_success_message' => $this->data($request, $language, 'claim_reward_driver_success_message'),
            'coffee_wall_heading_success_message' => $this->data($request, $language, 'coffee_wall_heading_success_message'),
            'coffee_wall_text_success_message' => $this->data($request, $language, 'coffee_wall_text_success_message'),
            'too_many_secured_cash_attempt_message' => $this->data($request, $language, 'too_many_secured_cash_attempt_message'),
            'secured_cash_success_message' => $this->data($request, $language, 'secured_cash_success_message'),
            'not_allowed_post_ride_state_wise_message' => $this->data($request, $language, 'not_allowed_post_ride_state_wise_message'),
            'add_your_phone' => $this->data($request, $language, 'add_your_phone'),
            'confirm_cancel_noshow' => $this->data($request, $language, 'confirm_cancel_noshow'),
            'cancel_noshow_take_me_back' => $this->data($request, $language, 'cancel_noshow_take_me_back'),
            'cancel_noshow_are_you_sure' => $this->data($request, $language, 'cancel_noshow_are_you_sure'),
            'popup_login_btn_text' => $this->data($request, $language, 'popup_login_btn_text'),
            'revert_arbitration_button' => $this->data($request, $language, 'revert_arbitration_button'),
            'cancel_button' => $this->data($request, $language, 'cancel_button'),
            'delete_button' => $this->data($request, $language, 'delete_button'),
            'no_show_driver_button' => $this->data($request, $language, 'no_show_driver_button'),
            'admin_block_account_message' => $this->data($request, $language, 'admin_block_account_message'),
            'popup_signup_btn_text' => $this->data($request, $language, 'popup_signup_btn_text'),
            'arbitration_success_message' => $this->data($request, $language, 'arbitration_success_message'),
            'ride_dead_time_text' => $this->data($request, $language, 'ride_dead_time_text'),
            'payout_request_success_message' => $this->data($request, $language, 'payout_request_success_message'),
            'suspended_account_phone_number_message' => $this->data($request, $language, 'suspended_account_phone_number_message'),
            'booking_request_success_message' => $this->data($request, $language, 'booking_request_success_message'),
            'login_before_booking_message' => $this->data($request, $language, 'login_before_booking_message'),
        ];
    }

    public function update($successMessagesSetting, $language, $request)
    {
        $fields = $this->fields($successMessagesSetting, $language, $request);

        $successMessagesSettingDetail = SuccessMessagesSettingDetail::where('messages_setting_id', $successMessagesSetting->id)
            ->where('language_id', $language->id)
            ->first();

        if (!$successMessagesSettingDetail) {
            // Create a new record if not exists
            SuccessMessagesSettingDetail::create($fields);
        } else {
            // Update existing record
            $successMessagesSettingDetail->update($fields);
        }
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
