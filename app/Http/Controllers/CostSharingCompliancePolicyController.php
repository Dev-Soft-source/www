<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notification;

class CostSharingCompliancePolicyController extends Controller
{
    public function index($lang = null)
    {
        $languages = Language::getAllCached();
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        }
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')
                ->where(function ($query) use ($user_id) {
                    $query->where('type', '1')
                        ->whereHas('ride', function ($q) use ($user_id) {
                            $q->where('added_by', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')
                        ->whereHas('booking', function ($q) use ($user_id) {
                            $q->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', null)
                        ->whereHas('receiver', function ($q) use ($user_id) {
                            $q->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('cost_sharing_compliance_policy', [
            'notifications' => $notifications,
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
        ]);
    }
}
