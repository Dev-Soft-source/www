<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Step1PageSettingResource;
use App\Models\Step1PageSetting;
use App\Services\Step1PageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\Step1PageSettingImport;
use App\Exports\Step1PageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class Step1PageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $step1PageSetting = Step1PageSetting::with(['step1PageSettingDetail', 'step1PageSettingDetail.language:id,name'])->first();
        if (!$step1PageSetting) {
            $step1PageSetting = Step1PageSetting::create([]);
            $step1PageSetting = Step1PageSetting::with(['step1PageSettingDetail', 'step1PageSettingDetail.language:id,name'])->find($step1PageSetting->id);
        }
        return $this->successResponse(new Step1PageSettingResource($step1PageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new Step1PageSettingService();
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

        $step1PageSetting = Step1PageSetting::first();
        if (!$step1PageSetting) {
            $step1PageSetting = Step1PageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($step1PageSetting, $language, $request);
        }

        if ($step1PageSetting) {
            return $this->successResponse([], "Step 1 of 5 page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Step 1 page settings via Excel.
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
                Excel::import(new Step1PageSettingImport($isAllLanguages ? null : $request->language_id), $request->file('excel_file'));

                if ($isAllLanguages) {
                    return $this->successResponse([], 'Step 1 page settings for all languages uploaded successfully from Excel.');
                }
                return $this->successResponse(['language' => $language->name], "Step 1 page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Step1 Page Excel upload error: ' . $e->getMessage());
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
            $existingData = $format === 'all_languages' ? Step1PageSetting::with('step1PageSettingDetail')->first() : null;
            $fileName = 'step1_page_settings_template_' . date('Y-m-d') . '.xlsx';
            return Excel::download(
                new Step1PageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Step1 Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
