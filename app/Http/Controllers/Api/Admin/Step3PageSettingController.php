<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Step3PageSettingResource;
use App\Models\Step3PageSetting;
use App\Services\Step3PageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\Step3PageSettingImport;
use App\Exports\Step3PageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class Step3PageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $step3PageSetting = Step3PageSetting::with(['step3PageSettingDetail', 'step3PageSettingDetail.language:id,name'])->first();
        if (!$step3PageSetting) {
            $step3PageSetting = Step3PageSetting::create([]);
            $step3PageSetting = Step3PageSetting::with(['step3PageSettingDetail', 'step3PageSettingDetail.language:id,name'])->find($step3PageSetting->id);
        }
        return $this->successResponse(new Step3PageSettingResource($step3PageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new Step3PageSettingService();
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

        $step3PageSetting = Step3PageSetting::first();
        if (!$step3PageSetting) {
            $step3PageSetting = Step3PageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($step3PageSetting, $language, $request);
        }

        if ($step3PageSetting) {
            return $this->successResponse([], "Step 3 of 4 page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Step 3 page settings via Excel.
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
                Excel::import(new Step3PageSettingImport($isAllLanguages ? null : $request->language_id), $request->file('excel_file'));

                if ($isAllLanguages) {
                    return $this->successResponse([], 'Step 3 page settings for all languages uploaded successfully from Excel.');
                }
                return $this->successResponse(['language' => $language->name], "Step 3 page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Step3 Page Excel upload error: ' . $e->getMessage());
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
            $existingData = $format === 'all_languages' ? Step3PageSetting::with('step3PageSettingDetail')->first() : null;
            $fileName = 'step3_page_settings_template_' . date('Y-m-d') . '.xlsx';
            return Excel::download(
                new Step3PageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Step3 Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
