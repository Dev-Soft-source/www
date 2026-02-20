<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PrivacyPolicyPageSettingResource;
use App\Models\PrivacyPolicyPageSetting;
use App\Services\PrivacyPolicyPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\PrivacyPolicyPageSettingImport;
use App\Exports\PrivacyPolicyPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PrivacyPolicyPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $privacyPolicyPageSetting = PrivacyPolicyPageSetting::with(['privacyPolicyPageSettingDetail', 'privacyPolicyPageSettingDetail.language:id,name'])->first();
        if (!$privacyPolicyPageSetting) {
            $privacyPolicyPageSetting = PrivacyPolicyPageSetting::create([]);
            $privacyPolicyPageSetting = PrivacyPolicyPageSetting::with(['privacyPolicyPageSettingDetail', 'privacyPolicyPageSettingDetail.language:id,name'])->find($privacyPolicyPageSetting->id);
        }
        return $this->successResponse(new PrivacyPolicyPageSettingResource($privacyPolicyPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new PrivacyPolicyPageSettingService();
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

        $privacyPolicyPageSetting = PrivacyPolicyPageSetting::first();
        if (!$privacyPolicyPageSetting) {
            $privacyPolicyPageSetting = PrivacyPolicyPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($privacyPolicyPageSetting, $language, $request);
        }

        if ($privacyPolicyPageSetting) {
            return $this->successResponse([], "Privacy policy page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Privacy Policy page settings via Excel (all-languages format: Field Name + one column per language).
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
                Excel::import(new PrivacyPolicyPageSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'Privacy Policy page settings for all languages uploaded successfully from Excel.'
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
            Log::error('Privacy Policy Page Excel upload error: ' . $e->getMessage());
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
                $existingData = PrivacyPolicyPageSetting::with('privacyPolicyPageSettingDetail')->first();
            }
            return Excel::download(
                new PrivacyPolicyPageSettingTemplateExport($format, $languages, $existingData),
                'privacy_policy_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Privacy Policy Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
