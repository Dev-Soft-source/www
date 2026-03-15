<?php

namespace App\View\Composers;

use App\Models\Country;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use Carbon\Carbon;
use Illuminate\View\View;

class BirthdayComposer
{
    /**
     * Share birthday data and postRidePage (for navbar modals) with the layout.
     */
    public function compose(View $view): void
    {
        $birthdayData = null;
        $postRidePage = null;
        $selectedLangAbbr = session('selectedLanguage');
        $selectedLanguage = $selectedLangAbbr ? Language::where('abbreviation', $selectedLangAbbr)->first() : null;
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        $defaultLang = Language::where('is_default', 1)->first();
        if ($selectedLanguage && $defaultLang) {
            $postRidePage = PostRidePageSettingDetail::getByLanguageWithFallback($selectedLanguage->id, $defaultLang->id);
        }
        $view->with('postRidePage', $postRidePage);
        $view->with('selectedLanguage', $selectedLanguage);

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

