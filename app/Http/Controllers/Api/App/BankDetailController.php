<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use App\Models\Bank;
use App\Models\Language;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\PayoutOptionSettingDetail;
use App\Traits\StatusResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BankDetailController extends Controller
{
    use StatusResponser;

    public function getBankDetail(Request $request){
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $userBankDetail = BankDetail::where('user_id', $user_id)->first();
        $banks = Bank::orderBy('name', 'asc')->get();

        $payoutOptionPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $payoutOptionPage = PayoutOptionSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $payoutOptionPage = PayoutOptionSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'email' => trans('validation.email'),
            'numeric' => trans('validation.numeric'),
            'min' => trans('validation.min.numeric'),
        ];

        $data = ['userBankDetail' => $userBankDetail, 'banks' => $banks, 'payoutOptionPage' => $payoutOptionPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Get user Bank Detail');
    }


    public function storeUpdateBankDetail(Request $request){
        Log::info('=== storeUpdateBankDetail START ===');
        Log::info('Request data received:', $request->all());

        $validated = $request->validate([
            'type' => 'required',
            'bank_name' => $request->type == 'bank' ? 'required' : 'nullable',
            'account_holder_name' => $request->type == 'bank' ? 'required' : 'nullable',
            'account_holder_number' => $request->type == 'bank' ? 'required|digits_between:7,12' : 'nullable',
            'branch' => $request->type == 'bank' ? 'required' : 'nullable',
            'branch_number' => $request->type == 'bank' ? 'required|digits:5' : 'nullable',
            'branch_address' => $request->type == 'bank' ? 'required' : 'nullable',
            'institution_number' => $request->type == 'bank' ? 'required|digits:3' : 'nullable',
            'account_holder_address' => $request->type == 'bank' ? 'required' : 'nullable',
            'paypal_email' => $request->type == 'paypal' ? 'required|email' : 'nullable',
        ], [
            'institution_number.digits' => 'Institution number must be exactly 3 digits',
            'branch_number.digits' => 'Branch number must be exactly 5 digits',
            'account_holder_number.digits_between' => 'Account number must be between 7 and 12 digits',
        ]);

        Log::info('Validation passed:', $validated);

        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        Log::info('User authenticated:', ['user_id' => $user_id, 'user_email' => $user->email ?? 'N/A']);

        $messages = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('bank_save_message','bank_detail_update_message','paypal_update_message', 'paypal_saved_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('bank_save_message','bank_detail_update_message','paypal_update_message', 'paypal_saved_message')->first();
            }
        }

        $message = "";

        $getBankDetail = BankDetail::where('user_id', $user_id)->first();
        Log::info('Existing bank detail query result:', ['found' => isset($getBankDetail) && !is_null($getBankDetail), 'bank_detail' => $getBankDetail ? $getBankDetail->toArray() : null]);

        if(isset($getBankDetail) && !is_null($getBankDetail)){
            Log::info('Updating existing bank detail');

            if($request->type == "paypal"){
                $message = $messages->paypal_update_message ?? null;
            }else{
                $message = $messages->bank_detail_update_message ??'Bank detail successfully updated';
            }


        }else{
            Log::info('Creating new bank detail');
            $getBankDetail = new  BankDetail();
            if($request->type == "paypal"){
                $message = $message->paypal_saved_message ?? 'Your PayPal account is now set up for payouts';
            }else{
                $message = $messages->bank_save_message;
            }

        }


        if($request->type == "paypal"){
            Log::info('Setting PayPal data:', ['paypal_email' => $request->paypal_email]);
            $getBankDetail->paypal_email = $request->paypal_email;
        }else{
            Log::info('Setting Bank data:', [
                'bank_id' => $request->bank_name,
                'bank_title' => $request->account_holder_name,
                'acc_no' => $request->account_holder_number,
                'branch' => $request->branch,
                'address' => $request->account_holder_address,
                'institution_number' => $request->institution_number,
                'branch_address' => $request->account_holder_branch_address,
                'branch_number' => $request->account_holder_branch_number,
            ]);
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

        Log::info('Bank detail before save:', $getBankDetail->toArray());

        try {
            $saveResult = $getBankDetail->save();
            Log::info('Save result:', ['success' => $saveResult, 'id' => $getBankDetail->id]);
        } catch (\Exception $e) {
            Log::error('Save failed with exception:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }

        Log::info('Bank detail after save:', $getBankDetail->toArray());
        Log::info('=== storeUpdateBankDetail END ===');

        $data = ['bankDetail' => $getBankDetail];
        return $this->successResponse($data, $message);

    }


    public function verifyBank(Request $request){

        $selectedLanguage = app()->getLocale() ?? 'en';
        $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
        $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('admin_sent_verify_amount_message', 'bank_already_verified_message','bank_verified_message','verify_amount_not_match_message','general_error_message')->first();


        $validated = $request->validate([
            'user_verify_amount' => 'required|numeric'
        ]);

        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $message = "";

        $getBankDetail = BankDetail::where('user_id', $user_id)->first();
        if(isset($getBankDetail) && !is_null($getBankDetail)){

            if($getBankDetail->status == "sent_amount"){
                return $this->apiErrorResponse($message->admin_sent_verify_amount_message ?? 'ProximaRide can not send any amount in your account please wait', 200);
            }elseif($getBankDetail->status == "verify"){
                return $this->apiErrorResponse($messages->bank_already_verified_message ?? 'Your bank account already verified', 200);
            }

            if($getBankDetail->admin_verify_amount == $request->user_verify_amount){

                $getBankDetail->user_verify_amount = $request->user_verify_amount;
                $getBankDetail->status = "verified";
                $getBankDetail->save();

                $data = ['bankDetail' => $getBankDetail];
                return $this->successResponse($data, $messages->bank_verified_message ?? "Bank detail verified successfully");
            }else{
                return $this->apiErrorResponse($messages->verify_amount_not_match_message ?? 'Your enter amount not match with ProximaRide send amount please check your amount', 200);
            }


        }else{
            return $this->apiErrorResponse($messages->general_error_message ?? 'No Bank detail find', 200);
        }

    }
}
