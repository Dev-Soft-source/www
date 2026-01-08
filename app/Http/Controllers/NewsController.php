<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage)->select('notification_delete_text')->first();
            $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage)->select('cancel_button','delete_button')->first();
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage)->select('notification_delete_text')->first();
            $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage)->select('cancel_button','delete_button')->first();
        }

        // Fetch articles where the articleDetail.language_id matches the selected language ID
        $articles = Article::whereHas('articleDetail', function ($query) use ($selectedLanguage) {
            $query->where('language_id', $selectedLanguage->id);
        })->with('articleDetail')->get();

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0');
            $notifications = $notifications->where(function ($query) use ($user_id) {
                $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                    $query->where('added_by', $user_id);
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        }
        return view('news',['notificationPage'=>$notificationPage ,'successMessage'=>$successMessage,'articles' => $articles,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }

    public function newsDetail($lang = null, $id){
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        // Fetch articles where the articleDetail.language_id matches the selected language ID
        $article = Article::whereId($id)->with('articleDetail')->first();

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                      ->whereHas('ride', function ($query) use ($user_id) {
                          $query->where('added_by', $user_id);
                      });
            })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is 2 and booking_id belongs to the user
                $query->where('type', '2')
                      ->whereHas('booking', function ($query) use ($user_id) {
                          $query->where('user_id', $user_id);
                      });
            })
            ->orWhere(function ($query) use ($user_id) {
                // Ratings where type is null and receiver_id belongs to the user
                $query->where('type', null)
                      ->whereHas('receiver', function ($query) use ($user_id) {
                          $query->where('id', $user_id);
                      });
            })
            ->orderBy('id', 'desc')
            ->get();
        }
        return view('news_detail',['article' => $article,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage]);
    }
}
