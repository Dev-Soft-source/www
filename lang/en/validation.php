<?php

return
	[
		'accepted' => 'The :attribute field must be accepted.',
		'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
		'active_url' => 'The :attribute field must be a valid URL.',
		'after' => 'The :attribute field must be a date after :date.',
		'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
		'alpha' => 'The :attribute field must only contain letters.',
		'alpha_dash' => 'The :attribute field must only contain letters, numbers, dashes and underscores.',
		'alpha_num' => 'The :attribute field must only contain letters and numbers.',
		'array' => 'The :attribute field must contain :size items.',
		'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
		'before' => 'The :attribute field must be a date before :date.',
		'before_or_equal' => 'The :attribute field must be a date before or equal to :date.',
		'between' =>
		[
			'array' => 'The :attribute field must have between :min and :max items.',
			'file' => 'The :attribute field must be between :min and :max kilobytes.',
			'numeric' => 'The :attribute field must be between :min and :max.',
			'string' => 'The :attribute field must be between :min and :max characters.',
		],
		'boolean' => 'The :attribute field must be true or false.',
		'can' => 'The :attribute field contains an unauthorized value.',
		'confirmed' => 'The :attribute field confirmation does not match.',
		'current_password' => 'The password is incorrect.',
		'date' => 'The :attribute field must be a valid date.',
		'date_equals' => 'The :attribute field must be a date equal to :date.',
		'date_format' => 'The :attribute field must match the format :format.',
		'decimal' => 'The :attribute field must have :decimal decimal places.',
		'declined' => 'The :attribute field must be declined.',
		'declined_if' => 'The :attribute field must be declined when :other is :value.',
		'different' => 'The :attribute field and :other must be different.',
		'digits' => 'The :attribute field must be :digits digits.',
		'digits_between' => 'The :attribute field must be between :min and :max digits.',
		'dimensions' => 'The :attribute field has invalid image dimensions.',
		'distinct' => 'The :attribute field has a duplicate value.',
		'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
		'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
		'email' => 'The :attribute field must be a valid email address.',
		'ends_with' => 'The :attribute field must end with one of the following: :values.',
		'enum' => 'The selected :attribute is invalid.',
		'exists' => 'The selected :attribute is invalid.',
		'extensions' => 'The :attribute field must have one of the following extensions: :values.',
		'file' => 'The :attribute field must be a file.',
		'filled' => 'The :attribute field must have a value.',
		'gt' =>
		[
			'array' => 'The :attribute field must have more than :value items.',
			'file' => 'The :attribute field must be greater than :value kilobytes.',
			'numeric' => 'The :attribute field must be greater than :value.',
			'string' => 'The :attribute field must be greater than :value characters.',
		],
		'gte' =>
		[
			'array' => 'The :attribute field must have :value items or more.',
			'file' => 'The :attribute field must be greater than or equal to :value kilobytes.',
			'numeric' => 'The :attribute field must be greater than or equal to :value.',
			'string' => 'The :attribute field must be greater than or equal to :value characters.',
		],
		'hex_color' => 'The :attribute field must be a valid hexadecimal color.',
		'image' => 'The :attribute field must be an image.',
		'in' => 'The selected :attribute is invalid.',
		'in_array' => 'The :attribute field must exist in :other.',
		'integer' => 'The :attribute field must be an integer.',
		'ip' => 'The :attribute field must be a valid IP address.',
		'ipv4' => 'The :attribute field must be a valid IPv4 address.',
		'ipv6' => 'The :attribute field must be a valid IPv6 address.',
		'json' => 'The :attribute field must be a valid JSON string.',
		'lowercase' => 'The :attribute field must be lowercase.',
		'lt' =>
		[
			'array' => 'The :attribute field must have less than :value items.',
			'file' => 'The :attribute field must be less than :value kilobytes.',
			'numeric' => 'The :attribute field must be less than :value.',
			'string' => 'The :attribute field must be less than :value characters.',
		],
		'lte' =>
		[
			'array' => 'The :attribute field must not have more than :value items.',
			'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
			'numeric' => 'The :attribute field must be less than or equal to :value.',
			'string' => 'The :attribute field must be less than or equal to :value characters.',
		],
		'mac_address' => 'The :attribute field must be a valid MAC address.',
		'max' =>
		[
			'array' => 'The :attribute field must not have more than :max items.',
			'file' => 'The :attribute field must not be greater than :max kilobytes.',
			'numeric' => 'The :attribute field must not be greater than :max.',
			'string' => 'The :attribute field must not be greater than :max characters.',
		],
		'max_digits' => 'The :attribute field must not have more than :max digits.',
		'mimes' => 'The :attribute field must be a file of type: :values.',
		'mimetypes' => 'The :attribute field must be a file of type: :values.',
		'min' =>
		[
			'array' => 'The :attribute field must have at least :min items.',
			'file' => 'The :attribute field must be at least :min kilobytes.',
			'numeric' => 'The :attribute field must be at least :min.',
			'string' => 'The :attribute field must be at least :min characters.',
		],
		'min_digits' => 'The :attribute field must have at least :min digits.',
		'missing' => 'The :attribute field must be missing.',
		'missing_if' => 'The :attribute field must be missing when :other is :value.',
		'missing_unless' => 'The :attribute field must be missing unless :other is in :values.',
		'missing_with' => 'The :attribute field must be missing when :values is present.',
		'missing_with_all' => 'The :attribute field must be missing when :values are present.',
		'multiple_of' => 'The :attribute field must be a multiple of :value.',
		'not_in' => 'The selected :attribute is invalid.',
		'not_regex' => 'The :attribute field format is invalid.',
		'numeric' => 'The :attribute field must be a number.',
		'password' => 'The password is incorrect.',
		'password_letters' => 'The :attribute field must contain at least one letter.',
		'password_mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
		'password_numbers' => 'The :attribute field must contain at least one number.',
		'password_symbols' => 'The :attribute field must contain at least one symbol.',
		'password_uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
		'present' => 'The :attribute field must be present.',
		'present_if' => 'The :attribute field must be present when :other is :value.',
		'present_unless' => 'The :attribute field must be present unless :other is in :values.',
		'present_with' => 'The :attribute field must be present when :values is present.',
		'present_with_all' => 'The :attribute field must be present when :values are present.',
		'prohibited' => 'The :attribute field is prohibited.',
		'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
		'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
		'prohibits' => 'The :attribute field prohibits :other from being present.',
		'regex' => 'The :attribute field format is invalid.',
		'required' => 'This field is required.',
		
		'required_array_keys' => 'The :attribute field must contain entries for: :values.',
		'required_if' => 'The :attribute field is required when :other is :value.',
		'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
		'required_unless' => 'The :attribute field is required unless :other is in :values.',
		'required_with' => 'The :attribute field is required when :values is present.',
		'required_with_all' => 'The :attribute field is required when :values are present.',
		'required_without' => 'The :attribute field is required when :values is not present.',
		'required_without_all' => 'The :attribute field is required when none of :values are present.',
		'same' => 'The :attribute field must match :other.',
		'size' =>
		[
			'array' => 'The :attribute field must contain :size items.',
			'file' => 'The :attribute field must be :size kilobytes.',
			'numeric' => 'The :attribute field must be :size.',
			'string' => 'The :attribute field must be :size characters.',
		],
		'starts_with' => 'The :attribute field must start with one of the following: :values.',
		'string' => 'The :attribute field must be a string.',
		'timezone' => 'The :attribute field must be a valid timezone.',
		'unique' => 'The :attribute has already been taken.',
		'uploaded' => 'The :attribute failed to upload.',
		'uppercase' => 'The :attribute field must be uppercase.',
		'url' => 'The :attribute field must be a valid URL.',
		'ulid' => 'The :attribute field must be a valid ULID.',
		'uuid' => 'The :attribute field must be a valid UUID.',
		'custom' =>
		[
			'make' => [
				'required' => 'The make is required.',
			],
			'model' => [
				'required' => 'The model is required.',
			],
			'type' => [
				'required' => 'The vehicle type is required.',
			],
			'liscense_no' => [
				'required' => 'The license number is required.',
				'max' => 'The license number must be less than 8 characters.',
			],
			'color' => [
				'required' => 'The color is required.',
				'max' => 'The color must be less than 15 characters.',
			],
			'year' => [
				'required' => 'The year is required.',
				'max' => 'The year must be less than 4 characters.',
			],
			'car_type' => [
				'required' => 'The car type is required.',
			],
			'primary_vehicle' => [
				'required' => 'The primary vehicle is required.',
			],
			'image' => [
				'required_without' => 'The image is required.',
				'image' => 'The image must be an image.',
				'mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
				'max' => 'The image must be less than 10MB.',
			],
			'city_not_in_record' => [
				'message' => 'We could not find this city name in our records, please double-check the spelling.',
			],
			'from' => [
				'required' => 'The origin is required.',
			],
			'to' => [
				'required' => 'The destination is required.',
			],
			'pickup' => [
				'required' => 'The pickup is required.',
			],
			'dropoff' => [
				'required' => 'The dropoff is required.',
			],
			'time' => [
				'required' => 'The time is required.',
			],
			'details' => [
				'required' => 'The details are required.',
			],
			'seats' => [
				'required' => 'Please select the number of seats.',
			],
			'smoke' => [
				'required' => 'Please select a smoking option.',
			],
			'animal_friendly' => [
				'required' => 'Please select an animal-friendly option.',
			],
			'booking_method' => [
				'required' => 'Please select a booking method.',
			],
			'luggage' => [
				'required' => 'Please select a luggage option.',
			],
			'price' => [
				'required' => 'The price is required.',
			],
			'payment_method' => [
				'required' => 'Please select a payment method.',
			],
			'card_id' => [
				'required' => 'Please select a payment option.',
			],
			'dr_amount' => [
				'required' => 'The amount is required.',
			],
			'dr_amount.gt' => 'The amount must be greater than 0.',
			'middle_seats' => [
				'required' => 'Please select middle seats.',
			],
			'back_seats' => [
				'required' => 'Please select back seats.',
			],
			'agree_terms' => [
				'accepted' => 'You must agree to the terms to continue.',
			],
			'email' =>
			[
				'required' => 'Email address is required.',
				'email' => 'Please enter a valid email address, such as name@example.com.',
				'unique' => 'An account with this email address already exists.',
			],
			'package' =>
			[
				'required' => 'The package is required.',
			],
			'custom_amount' =>
			[
				'required' => 'The custom amount is required.',
			],
			'name' =>
			[
				'required' => 'The name is required.',
			],
			'payment_method' =>
			[
				'required' => 'The payment method is required.',
				'in' => 'The payment method is invalid.',
			],
			'donation_acknowledgment' =>
			[
				'required' => 'The donation acknowledgment is required.',
			],
			'terms_privacy' =>
			[
				'required' => 'The terms and privacy are required.',
			],
			'name_on_card' =>
			[
				'required_if' => 'The cardholder’s name is required when paying by credit card.',
			],
			'card_element' =>
			[
				'required_if' => 'The card details are required when paying by credit card.',
			],
			'first_name' =>
			[
				'required' => 'First name is required.',
				'regex' => 'First name may only contain letters, spaces, and hyphens.',
			],
			'last_name' =>
			[
				'required' => 'Last name is required.',
				'regex' => 'Last name may only contain letters, spaces, and hyphens.',
			],
			'password' =>
			[
				'required' => 'Password is required.',
				'min' => 'Password must be at least :min characters.',
				'regex' => 'Your password must be at least 8 characters long, including a lowercase letter, an uppercase letter, a number, and a special character.',
			],
			'password_confirmation' =>
			[
				'required' => 'Confirm password is required.',
				'same' => 'The passwords do not match.',
			],
			'old_email' =>
			[
				'required' => 'The current email is required.',
				'email' => 'The current email is not a valid email address.',
			],
			'email_confirmation' =>
			[
				'required' => 'The confirm email is required.',
				'email' => 'The confirm email is not a valid email address.',
			],
			'pass1' =>
			[
				'required' => 'The current password is required.',
			],
			'pass2' =>
			[
				'required' => 'The new password is required.',
				'string' => 'The new password must be a string.',
				'min' => 'The new password must be at least :min characters.',
				'regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
			],
			'pass3' =>
			[
				'required' => 'The confirm password is required.',
				'string' => 'The confirm password must be a string.',
				'same' => 'The confirm password does not match the new password.',
			],
			'agree_cost_share_terms' =>
			[
				'required' => 'Please confirm you agree to the cost-sharing rule.',
				'accepted' => 'Please confirm you agree to the cost-sharing rule.',
			],
			'rideshare_disclaimer' =>
			[
				'required' => 'This acknowledgment is required before continuing.',
				'accepted' => 'This acknowledgment is required before continuing.',
			],
			'gender' =>
			[
				'required' => 'The gender is required.',
			],
			'dob' =>
			[
				'required' => 'The date of birth is required.',
				'date' => 'The date of birth must be a valid date.',
			],
			'country' =>
			[
				'required' => 'The country is required.',
			],
			'address' =>
			[
				'string' => 'The address must be a string.',
				'max' => 'The address may not be greater than :max characters.',
			],
			'state' =>
			[
				'required' => 'The state is required.',
				'string' => 'The state must be a string.',
				'max' => 'The state may not be greater than :max characters.',
			],
			'city' =>
			[
				'required' => 'The city is required.',
				'string' => 'The city must be a string.',
				'max' => 'The city may not be greater than :max characters.',
			],
			'zipcode' =>
			[
				'required' => 'The zip code is required.',
				'string' => 'The zip code must be a string.',
				'max' => 'The zip code may not be greater than :max characters.',
			],
			'government_issued_id' =>
			[
				'file' => 'The government issued id must be a file.',
				'mimes' => 'The government issued id must be a file of type: :values.',
				'max' => 'The government issued id must be less than 10MB.',
			],
			'bio' =>
			[
				'required' => 'The bio is required.',
				'string' => 'The bio must be a string.',
				'max' => 'The bio may not be greater than :max characters.',
			],
			'phone' =>
			[
				'required' => 'Phone number is required.',
				'valid' => 'Please enter a valid phone number.',
				'unique' => 'An account with this phone number already exists.',
			],
		],
		'attributes' =>
		[
			'make' => 'Make',
			'model' => 'Model',
			'type' => 'Vehicle Type',
			'color' => 'Color',
			'year' => 'Year',
			'liscense_no' => 'License Plate Number',
			'car_type' => 'Power Source',
			'primary_vehicle' => 'Primary Vehicle',
			'image' => 'Vehicle Photo',
			'email' => 'Email Address',
			'password' => 'Password',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'gender' => 'Gender',
			'dob' => 'Date of Birth',
			'country' => 'Country',
			'address' => 'Address',
			'state' => 'State',
			'city' => 'City',
			'zipcode' => 'Zip Code',
			'government_issued_id' => 'Government Issued ID',
			'bio' => 'Bio',
			'phone' => 'Phone Number',
			'name' => 'Name',
			'name_on_card' => 'Cardholder’s name',
			'card_element' => 'Card details',
			'title' => 'Title',
			'description' => 'Description',
			'pass1' => 'Current password',
			'pass2' => 'New password',
			'pass3' => 'Confirm password',
		],
	];
