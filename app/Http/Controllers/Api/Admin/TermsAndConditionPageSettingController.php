<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TermsAndConditionPageSettingResource;
use App\Models\TermsAndConditionPageSetting;
use App\Services\TermsAndConditionPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\TermsAndConditionPageSettingImport;
use App\Exports\TermsAndConditionPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class TermsAndConditionPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $termsAndConditionPageSetting = TermsAndConditionPageSetting::with(['termsAndConditionPageSettingDetail', 'termsAndConditionPageSettingDetail.language:id,name'])->first();
        if (!$termsAndConditionPageSetting) {
            $termsAndConditionPageSetting = TermsAndConditionPageSetting::create([]);
            $termsAndConditionPageSetting = TermsAndConditionPageSetting::with(['termsAndConditionPageSettingDetail', 'termsAndConditionPageSettingDetail.language:id,name'])->find($termsAndConditionPageSetting->id);
        }
        return $this->successResponse(new TermsAndConditionPageSettingResource($termsAndConditionPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new TermsAndConditionPageSettingService();
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

        $termsAndConditionPageSetting = TermsAndConditionPageSetting::first();
        if (!$termsAndConditionPageSetting) {
            $termsAndConditionPageSetting = TermsAndConditionPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($termsAndConditionPageSetting, $language, $request);
        }

        if ($termsAndConditionPageSetting) {
            return $this->successResponse([], "Terms and condition page setting updated successfully.");
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
                Excel::import(new TermsAndConditionPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Terms and Conditions page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Terms & Conditions Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new TermsAndConditionPageSettingTemplateExport($request->get('format', 'single_column')),
                'terms_and_conditions_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Terms & Conditions template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
