<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Step4PageSettingResource;
use App\Models\Step4PageSetting;
use App\Services\Step4PageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\Step4PageSettingImport;
use App\Exports\Step4PageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class Step4PageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $step4PageSetting = Step4PageSetting::query();
        
        $step4PageSetting = $step4PageSetting->with(['step4PageSettingDetail', 'step4PageSettingDetail.language:id,name']);
        $step4PageSetting = $step4PageSetting->first();

        return $this->successResponse($step4PageSetting ? new Step4PageSettingResource($step4PageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new Step4PageSettingService();
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

        $step4PageSetting = Step4PageSetting::first();
        if (!$step4PageSetting) {
            $step4PageSetting = Step4PageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($step4PageSetting, $language, $request);
        }

        if ($step4PageSetting) {
            return $this->successResponse([], "Step 4 of 5 page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Step 4 page settings via Excel.
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
            $import = new Step4PageSettingImport($languageId);

            try {
                Excel::import($import, $request->file('excel_file'));

                if ($languageId) {
                    $language = Language::find($languageId);
                    return $this->successResponse(
                        ['language' => $language->name],
                        "Step 4 page settings for {$language->name} uploaded successfully from Excel."
                    );
                }
                return $this->successResponse(
                    [],
                    'Step 4 of 5 page settings for all languages uploaded successfully from Excel.'
                );
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
            Log::error('Step4 Page Settings Excel upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Excel template for Step 4 page settings.
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
                $existingData = Step4PageSetting::with('step4PageSettingDetail')->first();
            }         
            $fileName = 'step4_page_settings_template_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new Step4PageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Step4 Page Settings template download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download template: ' . $e->getMessage(),
            ], 500);
        }
    }
}