<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Language;
use App\Models\MyReviewSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\PayoutOptionSettingDetail;
use App\Models\Notification;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PayoutController extends Controller
{
    public function index($lang = null){
        
        $payoutOptionPage = PayoutOptionSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfilePage = ProfilePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $ProfileSetting = ProfileSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $reviewSetting = MyReviewSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            $banks = Bank::orderBy('name','asc')->get();

            $userBankDetail = BankDetail::where('user_id', $user_id)->first();

            return view('payout',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,
            'ProfileSetting' => $ProfileSetting,'user' => $user,'banks' => $banks,'userBankDetail' => $userBankDetail,
            'payoutOptionPage' => $payoutOptionPage]);
        } else {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation, 'payoutOptionPage' => $payoutOptionPage]);
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;

        $message = '';
        $messages = null;
        $niceNames = [];

        $selectedLanguageAbbr = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        if ($selectedLanguage) {
            $selectedLanguageAbbr = $selectedLanguage->abbreviation;
            $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)
                ->select('bank_save_message', 'paypal_update_message', 'paypal_saved_message', 'bank_detail_update_message')
                ->first();

            $payOut = PayoutOptionSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $niceNames = [
                'bank_name' => isset($payOut->bank_error) ? $payOut->bank_error : '',
                'institution_number' => isset($payOut->bank_error) ? $payOut->institute_no_error : '',
                'branch' => isset($payOut->bank_error) ? $payOut->branch_error : '',
                'branch_address' => isset($payOut->bank_error) ? $payOut->branch_address_error : '',
                'branch_number' => isset($payOut->bank_error) ? $payOut->branch_no_error : '',
                'account_holder_number' => isset($payOut->bank_error) ? $payOut->acc_no_error : '',
                'account_holder_address' => isset($payOut->bank_error) ? $payOut->address_error : '',
                'account_holder_name' => isset($payOut->bank_error) ? $payOut->bank_title_error : '',
            ];
        } else {
            $selectedLanguageAbbr = app()->getLocale() ?? 'en';
        }

        $payoutMethod = (string) $request->input('payout_method');
        $isPaypal = $payoutMethod === 'paypal';
        $isBank = $payoutMethod === 'bank';
        $isInterac = $payoutMethod === 'interac';

        $validated = $request->validate([
            'payout_method' => 'required|in:interac,bank,paypal',

            // Interac fields (persist only if your DB schema supports it)
            'interac_email' => $isInterac ? 'required|email' : 'nullable',
            'interac_email_confirm' => $isInterac ? 'required|same:interac_email' : 'nullable',
            'interac_autodeposit' => $isInterac ? 'accepted' : 'nullable',

            // Bank fields
            'account_holder_name' => $isBank ? 'required' : 'nullable',
            'account_holder_number' => $isBank ? 'required|digits_between:7,12' : 'nullable',
            'branch_number' => $isBank ? 'required|digits:5' : 'nullable',
            'institution_number' => $isBank ? 'required|digits:3' : 'nullable',

            // Paypal fields
            'paypal_email' => $isPaypal ? 'required|email' : 'nullable',
            'paypal_email_confirm' => $isPaypal ? 'required|same:paypal_email' : 'nullable',
        ], [
            'interac_autodeposit.accepted' => 'Please enable Autodeposit for Interac withdrawals.',
            'paypal_email_confirm.required' => 'Please confirm your PayPal email.',
            'paypal_email_confirm.same' => 'PayPal emails must match.',
            'interac_email_confirm.required' => 'Please confirm your Interac email.',
            'interac_email_confirm.same' => 'Interac emails must match.',
        ], $niceNames);

        $getBankDetail = BankDetail::where('user_id', $user_id)->first();
        $existing = (bool) $getBankDetail;
        if (!$existing) {
            $getBankDetail = new BankDetail();
        }

        // Choose success message (null-safe)
        if ($existing) {
            $message = $isPaypal
                ? (optional($messages)->paypal_update_message ?? 'PayPal account successfully updated')
                : (optional($messages)->bank_detail_update_message ?? 'Bank detail successfully updated');
        } else {
            $message = $isPaypal
                ? (optional($messages)->paypal_saved_message ?? 'Your PayPal account is now set up for payouts')
                : (optional($messages)->bank_save_message ?? 'Your bank details are now set up for payouts');
        }

        if ($isPaypal) {
            $getBankDetail->paypal_email = $request->input('paypal_email');
        } elseif ($isBank) {
            $getBankDetail->bank_title = $request->input('account_holder_name');
            $getBankDetail->acc_no = $request->input('account_holder_number');
            $getBankDetail->institution_number = $request->input('institution_number');

            // Your Blade sends `branch_address` and also may send the legacy keys below.
            $getBankDetail->branch_number = $request->input('branch_number')?? $request->input('account_holder_branch_number');
        } elseif ($isInterac) {
            $getBankDetail->interac_email = $request->input('interac_email');
            if (!$existing) {
                // On first-time interac selection, ensure set_default is valid for downstream.
                $getBankDetail->set_default = 'interac';
            }
        }

        $getBankDetail->user_id = $user_id;

        if (empty($getBankDetail->status) || $getBankDetail->status === 'pending') {
            $getBankDetail->status = 'pending';
        }

        if ($isBank) {
            $getBankDetail->set_default = $request->input('set_default', 'bank');
        } elseif ($isPaypal) {
            $getBankDetail->set_default = $request->input('set_default', 'paypal');
        } elseif ($isInterac) {
            $getBankDetail->set_default = 'interac';
        }

        $getBankDetail->save();

        return redirect()->route('payout', ['lang' => $selectedLanguageAbbr])->with('message', $message);
    }

    public function verifyBank(Request $request){


        $selectedLanguage = app()->getLocale() ?? 'en';

        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

        $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('admin_sent_verify_amount_message', 'bank_already_verified_message','bank_verified_message','verify_amount_not_match_message','general_error_message')->first();

        $validated = $request->validate([
            'user_verify_amount' => 'required|numeric'
        ]);

        $user = auth()->user();
        $user_id = $user->id;

        $message = "";

        $getBankDetail = BankDetail::where('user_id', $user_id)->first();

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }

        if(isset($getBankDetail) && !is_null($getBankDetail)){
            if($getBankDetail->status == "sent_amount"){
                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', optional($message)->admin_sent_verify_amount_message ?? 'ProximaRide can not send any amount in your account please wait');
            }elseif($getBankDetail->status == "verify"){
                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', optional($message)->bank_already_verified_message ?? 'Your bank account already verified');
            }

            if($getBankDetail->admin_verify_amount == $request->user_verify_amount){
                $getBankDetail->user_verify_amount = $request->user_verify_amount;
                $getBankDetail->status = "verified";
                $getBankDetail->save();

                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', optional($message)->bank_verified_message ?? 'Bank detail verified successfully');
            }else{
                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', optional($message)->verify_amount_not_match_message ?? 'Your enter amount not match with ProximaRide send amount please check your amount');
            }
        }else{
            return $this->apiErrorResponse($message->general_error_message ?? 'No Bank detail find', 200);
        }
    }
}