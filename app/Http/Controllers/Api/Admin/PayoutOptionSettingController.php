<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Models\PayoutOptionSetting;
use App\Http\Controllers\Controller;
use App\Services\PayoutSettingService;
use App\Http\Resources\Admin\PayoutOptionSettingResource;
use App\Imports\PayoutOptionSettingImport;
use App\Exports\PayoutOptionSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PayoutOptionSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $payoutSetting = PayoutOptionSetting::with(['payoutOptionSettingDetail', 'payoutOptionSettingDetail.language:id,name'])->first();
        if (!$payoutSetting) {
            $payoutSetting = PayoutOptionSetting::create([]);
            $payoutSetting = PayoutOptionSetting::with(['payoutOptionSettingDetail', 'payoutOptionSettingDetail.language:id,name'])->find($payoutSetting->id);
        }
        return $this->successResponse(new PayoutOptionSettingResource($payoutSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new PayoutSettingService();
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

        $payoutOptionSettingDetail = PayoutOptionSetting::first();
        if (!$payoutOptionSettingDetail) {
            $payoutOptionSettingDetail = PayoutOptionSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($payoutOptionSettingDetail, $language, $request);
        }

        if ($payoutOptionSettingDetail) {
            return $this->successResponse([], "Payout option page setting updated successfully.");
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
                Excel::import(new PayoutOptionSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Payout option settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Payout Option Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new PayoutOptionSettingTemplateExport($request->get('format', 'single_column')),
                'payout_option_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Payout Option template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
