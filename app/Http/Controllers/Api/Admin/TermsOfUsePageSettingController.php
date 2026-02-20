<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TermsOfUsePageSettingResource;
use App\Models\TermsOfUsePageSetting;
use App\Services\TermsOfUsePageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\TermsOfUsePageSettingImport;
use App\Exports\TermsOfUsePageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class TermsOfUsePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $termsOfUsePageSetting = TermsOfUsePageSetting::with(['termsOfUsePageSettingDetail', 'termsOfUsePageSettingDetail.language:id,name'])->first();
        if (!$termsOfUsePageSetting) {
            $termsOfUsePageSetting = TermsOfUsePageSetting::create([]);
            $termsOfUsePageSetting = TermsOfUsePageSetting::with(['termsOfUsePageSettingDetail', 'termsOfUsePageSettingDetail.language:id,name'])->find($termsOfUsePageSetting->id);
        }
        return $this->successResponse(new TermsOfUsePageSettingResource($termsOfUsePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new TermsOfUsePageSettingService();
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

        $termsOfUsePageSetting = TermsOfUsePageSetting::first();
        if (!$termsOfUsePageSetting) {
            $termsOfUsePageSetting = TermsOfUsePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($termsOfUsePageSetting, $language, $request);
        }

        if ($termsOfUsePageSetting) {
            return $this->successResponse([], "Terms of use page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Terms of Use page settings via Excel.
     * When language_id is present: single-language format. When absent: all_languages format (Field Name + one column per language).
     */
    public function uploadExcel(Request $request)
    {
        try {
            $rules = [
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ];
            $messages = [
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ];
            if ($request->has('language_id')) {
                $rules['language_id'] = 'required|exists:languages,id';
                $messages['language_id.required'] = 'Please select a language';
                $messages['language_id.exists'] = 'Selected language does not exist';
            }
            $request->validate($rules, $messages);

            $languageId = $request->input('language_id');
            $import = new TermsOfUsePageSettingImport($languageId);

            try {
                Excel::import($import, $request->file('excel_file'));

                if ($languageId) {
                    $language = Language::find($languageId);
                    return $this->successResponse(
                        ['language' => $language->name],
                        "Terms of Use page settings for {$language->name} uploaded successfully from Excel."
                    );
                }
                return $this->successResponse(
                    [],
                    'Terms of use page settings for all languages uploaded successfully from Excel.'
                );
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => array_map(fn($f) => [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors(),
                        'values' => $f->values(),
                    ], $e->failures()),
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Terms of Use Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    /**
     * Download Excel template for Terms of Use page settings.
     * format=all_languages: Field Name + one column per language (with current DB values if any).
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');

            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = TermsOfUsePageSetting::with('termsOfUsePageSettingDetail')->first();
            }

            $fileName = 'terms_of_use_page_settings_template_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new TermsOfUsePageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Terms of Use template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
