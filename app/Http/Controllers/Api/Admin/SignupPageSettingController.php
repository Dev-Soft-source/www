<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SignupPageSettingResource;
use App\Models\SignupPageSetting;
use App\Services\SignupPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\SignupPageSettingImport;
use App\Exports\SignupPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class SignupPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $signupPageSetting = SignupPageSetting::with(['signupPageSettingDetail', 'signupPageSettingDetail.language:id,name'])->first();
        if (!$signupPageSetting) {
            $signupPageSetting = SignupPageSetting::create([]);
            $signupPageSetting = SignupPageSetting::with(['signupPageSettingDetail', 'signupPageSettingDetail.language:id,name'])->find($signupPageSetting->id);
        }
        return $this->successResponse(new SignupPageSettingResource($signupPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new SignupPageSettingService();
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

        $signupPageSetting = SignupPageSetting::first();
        if (!$signupPageSetting) {
            $signupPageSetting = SignupPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($signupPageSetting, $language, $request);
        }

        if ($signupPageSetting) {
            return $this->successResponse([], "Signup page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Signup page settings via Excel.
     * All-languages: no language_id, Excel has Field Name + one column per language.
     * Single-language: language_id required.
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
                Excel::import(new SignupPageSettingImport($isAllLanguages ? null : $request->language_id), $request->file('excel_file'));

                if ($isAllLanguages) {
                    return $this->successResponse([], 'Signup page settings for all languages uploaded successfully from Excel.');
                }
                return $this->successResponse(['language' => $language->name], "Signup page settings for {$language->name} uploaded successfully from Excel.");
            } catch (ValidationException $e) {
                $errors = [];
                foreach ($e->failures() as $failure) {
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
            Log::error('Signup Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    /**
     * Download template. format=all_languages (default): Field Name + one column per language.
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');
            $languages = $format === 'all_languages' ? Language::orderBy('id')->get() : null;
            $existingData = $format === 'all_languages' ? SignupPageSetting::with('signupPageSettingDetail')->first() : null;
            $fileName = 'signup_page_settings_template_' . date('Y-m-d') . '.xlsx';
            return Excel::download(
                new SignupPageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Signup Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
