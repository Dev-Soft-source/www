<?php

namespace App\View\Composers;

use App\Models\Country;
use Carbon\Carbon;
use Illuminate\View\View;

class BirthdayComposer
{
    /**
     * Share birthday data with the layout when it's the user's birthday.
     */
    public function compose(View $view): void
    {
        $birthdayData = null;

        if (auth()->check()) {
            $user = auth()->user();
            if (! empty($user->dob)) {
                try {
                    $dob = Carbon::parse($user->dob);
                    $today = Carbon::today();

                    if ($dob->month === $today->month && $dob->day === $today->day) {
                        $country = $user->country ? Country::find($user->country) : null;
                        $isoCode = $country && $country->iso_code ? strtolower($country->iso_code) : 'us';

                        $birthdayData = [
                            'user_id' => $user->id,
                            'username' => $user->first_name ?: ($user->username ?: 'Friend'),
                            'profile_image' => $user->profile_image ?? '',
                            'flag_url' => "https://flagcdn.com/w80/{$isoCode}.png",
                            'has_profile_image' => ! empty($user->getRawOriginal('profile_image')),
                        ];
                    }
                } catch (\Exception $e) {
                    // Invalid dob format - skip
                }
            }
        }

        $view->with('birthdayData', $birthdayData);
    }
}
