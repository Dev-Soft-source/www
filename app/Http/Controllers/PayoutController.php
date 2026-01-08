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

class PayoutController extends Controller
{
    public function index($lang = null){
        $languages = Language::all();
        $payoutOptionPage = null;
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $ProfilePage = null;
        $ProfileSetting = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $payoutOptionPage = PayoutOptionSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $payoutOptionPage = PayoutOptionSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfilePage = ProfilePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $ProfileSetting = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            $reviewSetting = MyReviewSettingDetail::where('language_id', $selectedLanguage->id)->select('review_left_label', 'review_received_label')->first();
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $user = User::whereId($user_id)->first();
            $banks = Bank::orderBy('name','asc')->get();

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

            $userBankDetail = BankDetail::where('user_id', $user_id)->first();

            return view('payout',['reviewSetting' => $reviewSetting,'ProfilePage' => $ProfilePage,'ProfileSetting' => $ProfileSetting,'user' => $user,'banks' => $banks,'userBankDetail' => $userBankDetail,'notifications' => $notifications,'languages' => $languages,'selectedLanguage' => $selectedLanguage, 'payoutOptionPage' => $payoutOptionPage]);
        } else {
            return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation, 'payoutOptionPage' => $payoutOptionPage]);
        }
    }

    public function store(Request $request)
    {
        
        
        $user = auth()->user();
        $user_id = $user->id;
        
        $message = "";
        $messages = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('bank_save_message', 'paypal_update_message', 'paypal_saved_message', 'bank_detail_update_message')->first();
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
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('bank_save_message', 'paypal_update_message', 'paypal_saved_message', 'bank_detail_update_message')->first();
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
            }
        }
        $validated = $request->validate([
            'payout_method' => 'required',
            'bank_name' => $request->payout_method == 'bank' ? 'required' : 'nullable',
            'account_holder_name' => $request->payout_method == 'bank' ? 'required' : 'nullable',
            'account_holder_number' => $request->payout_method == 'bank' ? 'required|digits_between:7,12' : 'nullable',
            'branch' => $request->payout_method == 'bank' ? 'required' : 'nullable',
            'branch_number' => $request->payout_method == 'bank' ? 'required|digits:5' : 'nullable',
            'branch_address' => $request->payout_method == 'bank' ? 'required' : 'nullable',
            'institution_number' => $request->payout_method == 'bank' ? 'required|digits:3' : 'nullable',
            'account_holder_address' => $request->payout_method == 'bank' ? 'required' : 'nullable',
            'paypal_email' => $request->payout_method == 'paypal' ? 'required|email' : 'nullable',
        ], [
            'institution_number.digits' => 'Institution number must be exactly 3 digits',
            'branch_number.digits' => 'Branch number must be exactly 5 digits',
            'account_holder_number.digits_between' => 'Account number must be between 7 and 12 digits',
        ], $niceNames);

        $getBankDetail = BankDetail::where('user_id', $user_id)->first();
        if(isset($getBankDetail) && !is_null($getBankDetail)){
            if($request->payout_method == "paypal"){
                $message = $messages->paypal_update_message;
            }else{
                $message = $messages->bank_detail_update_message ?? 'Bank detail successfully updated';
            }
        }else{
            $getBankDetail = new  BankDetail();
            if($request->payout_method == "paypal"){
                $message = $messages->paypal_saved_message ?? 'Your PayPal account is now set up for payouts';
            }else{
                $message = $messages->bank_save_message;
            }
        }

        if($request->payout_method == "paypal"){
            $getBankDetail->paypal_email = $request->paypal_email;
        }else{
            $getBankDetail->bank_id = $request->bank_name;
            $getBankDetail->bank_title = $request->account_holder_name;
            $getBankDetail->acc_no = $request->account_holder_number;
            $getBankDetail->branch = $request->branch;
            $getBankDetail->address = $request->account_holder_address;
            $getBankDetail->institution_number = $request->institution_number;
            $getBankDetail->branch_address = $request->account_holder_branch_address;
            $getBankDetail->branch_number = $request->account_holder_branch_number;
        }

        $getBankDetail->user_id = $user_id;
        if(isset($getBankDetail->status) && $getBankDetail->status != "pending"){

        }else{
            $getBankDetail->status =  "pending";
        }
        
        $getBankDetail->set_default = isset($request->set_default) ? $request->set_default : "bank";
        $getBankDetail->save();
        
        // Redirect
        return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', $message);
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
                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->admin_sent_verify_amount_message ?? 'ProximaRide can not send any amount in your account please wait');
            }elseif($getBankDetail->status == "verify"){
                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->bank_already_verified_message ?? 'Your bank account already verified');
            }

            if($getBankDetail->admin_verify_amount == $request->user_verify_amount){
                $getBankDetail->user_verify_amount = $request->user_verify_amount;
                $getBankDetail->status = "verified";
                $getBankDetail->save();

                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->bank_verified_message ?? 'Bank detail verified successfully');
            }else{
                return redirect()->route('payout', ['lang' => $selectedLanguage->abbreviation])->with('message', $message->verify_amount_not_match_message ?? 'Your enter amount not match with ProximaRide send amount please check your amount');
            }
        }else{
            return $this->apiErrorResponse($message->general_error_message ?? 'No Bank detail find', 200);
        }
    }
}