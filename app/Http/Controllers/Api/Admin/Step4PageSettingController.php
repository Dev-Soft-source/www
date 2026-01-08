<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Step5PageSettingResource;
use App\Models\Step5PageSetting;
use App\Services\Step5PageSettingService;
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
        $step5PageSetting = Step5PageSetting::query();
        
        $step5PageSetting = $step5PageSetting->with(['step5PageSettingDetail', 'step5PageSettingDetail.language:id,name']);
        $step5PageSetting = $step5PageSetting->first();

        return $this->successResponse($step5PageSetting ? new Step5PageSettingResource($step5PageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new Step5PageSettingService();
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

        $step5PageSetting = Step5PageSetting::first();
        if (!$step5PageSetting) {
            $step5PageSetting = Step5PageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($step5PageSetting, $language, $request);
        }

        if ($step5PageSetting) {
            return $this->successResponse([], "Step 4 of 5 page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Step 4 page settings via Excel
     */
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'language_id' => 'required|exists:languages,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ], [
                'language_id.required' => 'Please select a language',
                'language_id.exists' => 'Selected language does not exist',
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
            ]);

            $languageId = $request->language_id;
            $language = Language::find($languageId);
            if (!$language) return $this->errorResponse('Language not found', 404);

            try {
                $import = new Step4PageSettingImport($languageId);
                Excel::import($import, $request->file('excel_file'));

                return $this->successResponse(
                    ['language' => $language->name],
                    "Step 4 page settings for {$language->name} uploaded successfully from Excel."
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
        } catch (\Exception $e) {
            Log::error('Step4 Page Settings Excel upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Excel template for Step 4 page settings
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'single_column');
            $fileName = 'step4_page_settings_template_' . date('Y-m-d') . '.xlsx';
            return Excel::download(new Step4PageSettingTemplateExport($format), $fileName);
        } catch (\Exception $e) {
            Log::error('Step4 Page Settings template download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download template: ' . $e->getMessage(),
            ], 500);
        }
    }
}