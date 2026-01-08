<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BankDetailResource;
use App\Models\BankDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class BankSettingController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $bankSetting = BankDetail::query();
        $bankSetting = $bankSetting->where('type', 'admin')->with('bank')->first();

        return $this->successResponse($bankSetting ? new BankDetailResource($bankSetting) : [], 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $rules = [
            'bank_id' => ['required'],
            'set_default' => ['required'],
        ];

        if ($request->input('set_default') === 'bank') {
            $rules = array_merge($rules, [
                'bank_title' => ['required'],
                'acc_no' => ['required', 'numeric', 'digits:6'],
                'iban' => ['required', 'numeric', 'digits:3'],
                'branch' => ['required', 'numeric', 'digits:5'],
                'address' => ['required'],
            ]);
        }

        if ($request->input('set_default') === 'paypal') {
            $rules = array_merge($rules, [
                'paypal_email' => ['required'],
            ]);
        }

        $this->validate($request, $rules);

        $setting = null;
        $result = null;

        $bankSetting = BankDetail::where('type', 'admin')->first();
        if (!$bankSetting) {
            $setting = BankDetail::create([
                'bank_id' => $request->bank_id,
                'type' => 'admin',
                'bank_title' => $request->bank_title,
                'acc_no' => $request->acc_no,
                'iban' => $request->iban,
                'branch' => $request->branch,
                'address' => $request->address,
                'paypal_email' => $request->paypal_email,
                'set_default' => $request->set_default,
            ]);
        } else {
            $result = BankDetail::whereId($bankSetting->id)->update([
                'bank_id' => $request->bank_id,
                'bank_title' => $request->bank_title,
                'acc_no' => $request->acc_no,
                'iban' => $request->iban,
                'branch' => $request->branch,
                'address' => $request->address,
                'paypal_email' => $request->paypal_email,
                'set_default' => $request->set_default,
            ]);
        }

        if ($result || $setting) {
            return $this->apiSuccessResponse(new BankDetailResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
