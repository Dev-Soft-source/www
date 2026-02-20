<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RefundPolicyPageSettingResource;
use App\Models\RefundPolicyPageSetting;
use App\Services\RefundPolicyPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\RefundPolicyPageSettingImport;
use App\Exports\RefundPolicyPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class RefundPolicyPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $refundPolicyPageSetting = RefundPolicyPageSetting::with(['refundPolicyPageSettingDetail', 'refundPolicyPageSettingDetail.language:id,name'])->first();
        if (!$refundPolicyPageSetting) {
            $refundPolicyPageSetting = RefundPolicyPageSetting::create([]);
            $refundPolicyPageSetting = RefundPolicyPageSetting::with(['refundPolicyPageSettingDetail', 'refundPolicyPageSettingDetail.language:id,name'])->find($refundPolicyPageSetting->id);
        }
        return $this->successResponse(new RefundPolicyPageSettingResource($refundPolicyPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new RefundPolicyPageSettingService();
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

        $termsOfUsePageSetting = RefundPolicyPageSetting::first();
        if (!$termsOfUsePageSetting) {
            $termsOfUsePageSetting = RefundPolicyPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($termsOfUsePageSetting, $language, $request);
        }

        if ($termsOfUsePageSetting) {
            return $this->successResponse([], "Refund policy page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload refund policy page settings via Excel file.
     * All-languages mode: no language_id, Excel has Field Name + one column per language.
     * Single-language mode: language_id required, Excel has one language column.
     */
    public function uploadExcel(Request $request)
    {
        try {
            $isAllLanguages = !$request->has('language_id') || $request->language_id === null || $request->language_id === '';

            if ($isAllLanguages) {
                $request->validate([
                    'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
                ], [
                    'excel_file.required' => 'Please upload an Excel file',
                    'excel_file.file' => 'The uploaded file is not valid',
                    'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                    'excel_file.max' => 'The file size must not exceed 5MB',
                ]);
            } else {
                $request->validate([
                    'language_id' => 'required|exists:languages,id',
                    'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
                ]);
            }

            $language = $isAllLanguages ? null : Language::find($request->language_id);
            if (!$isAllLanguages && !$language) {
                return $this->errorResponse('Language not found', 404);
            }

            try {
                Excel::import(new RefundPolicyPageSettingImport($isAllLanguages ? null : $request->language_id), $request->file('excel_file'));

                if ($isAllLanguages) {
                    return $this->successResponse([], 'Refund policy page settings for all languages uploaded successfully from Excel.');
                }
                return $this->successResponse(['language' => $language->name], "Refund Policy page settings for {$language->name} uploaded successfully from Excel.");
            } catch (ValidationException $e) {
                $failures = $e->failures();
                $errors = [];
                foreach ($failures as $failure) {
                    $errors[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                        'values' => $failure->values(),
                    ];
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => $errors,
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Refund Policy Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    /**
     * Download Excel template. format=all_languages: Field Name + one column per language (with current DB values). Other: single_column.
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');

            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = RefundPolicyPageSetting::with('refundPolicyPageSettingDetail')->first();
            }

            $fileName = 'refund_policy_page_settings_template_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new RefundPolicyPageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Refund Policy Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
