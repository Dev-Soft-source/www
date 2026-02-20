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

    /**
     * Upload Payout Option settings via Excel (all-languages format: Field Name + one column per language).
     */
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ], [
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.file' => 'The uploaded file is not valid',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ]);

            try {
                Excel::import(new PayoutOptionSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'Payout option settings for all languages uploaded successfully from Excel.'
                );
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => array_map(fn ($f) => [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors(),
                    ], $e->failures()),
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Payout Option Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download Excel template. format=all_languages (default): Field Name + one column per language.
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');
            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = PayoutOptionSetting::with('payoutOptionSettingDetail')->first();
            }
            return Excel::download(
                new PayoutOptionSettingTemplateExport($format, $languages, $existingData),
                'payout_option_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Payout Option template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
