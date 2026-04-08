<?php

namespace App\Http\Controllers;

use App\Models\CancellationPageSettingDetail;
use App\Models\ChatsPageSettingDetail;
use App\Models\FirmCancellationPageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;

class CancellationPolicyController extends Controller
{
    public function index($lang = null)
    {
        
        $notificationPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $successMessage = $this->successMessage;
        $cancellationPolicyPage = CancellationPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);


        
        return view('cancellation_policy', ['notificationPage' => $notificationPage, 
        'successMessage' => $successMessage, 'cancellationPolicyPage' => $cancellationPolicyPage]);
    }



    public function firmCancellation($lang = null)
    {
        
        $notificationPage = ChatsPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $successMessage = $this->successMessage;
        $cancellationPolicyPage = FirmCancellationPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

       
        return view('firm_cancellation_policy', [
            'notificationPage' => $notificationPage, 
            'successMessage' => $successMessage, 
            'cancellationPolicyPage' => $cancellationPolicyPage, 
            ]);
    }
}
