<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\MyWalletSetting;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\MyWalletSettingService;
use App\Http\Resources\Admin\MyWalletSettingResource;
use App\Imports\MyWalletSettingImport;
use App\Exports\MyWalletSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyWalletSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myWalletSetting = MyWalletSetting::with(['myWalletSettingDetail', 'myWalletSettingDetail.language:id,name'])->first();
        if (!$myWalletSetting) {
            $myWalletSetting = MyWalletSetting::create([]);
            $myWalletSetting = MyWalletSetting::with(['myWalletSettingDetail', 'myWalletSettingDetail.language:id,name'])->find($myWalletSetting->id);
        }
        return $this->successResponse(new MyWalletSettingResource($myWalletSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyWalletSettingService();
        $response = $pageSettingService->validation($languages, $validationRule, $errorMessages);
        $validationRule = $response['validation_rules'];
        $errorMessages = $response['error_messages'];
        $niceNames = $response['nice_names'];

        $this->validate(
            $request,
            $validationRule,
            $errorMessages,
            $niceNames
        );

        $myWalletSettingDetail = MyWalletSetting::first();
        if (!$myWalletSettingDetail) {
            $myWalletSettingDetail = MyWalletSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myWalletSettingDetail, $language, $request);
        }

        if ($myWalletSettingDetail) {
            return $this->successResponse([], "My Wallet setting updated successfully.");
        }

        return $this->errorResponse();
    }

    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'language_id' => 'required|exists:languages,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ]);

            $language = Language::find($request->language_id);
            if (!$language) return $this->errorResponse('Language not found', 404);

            try {
                Excel::import(new MyWalletSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Wallet settings for {$language->name} uploaded successfully from Excel.");
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => array_map(fn($f) => [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors(),
                    ], $e->failures()),
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('My Wallet Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyWalletSettingTemplateExport($request->get('format', 'single_column')),
                'my_wallet_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Wallet template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
