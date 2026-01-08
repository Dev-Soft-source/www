<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Step4PageSettingResource;
use App\Models\Step4PageSetting;
use App\Services\Step4PageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\Step5PageSettingImport;
use App\Exports\Step5PageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class Step5PageSettingController extends Controller
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
            return $this->successResponse([], "Step 5 of 5 page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Step 5 page settings via Excel
     */
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'language_id' => 'required|exists:languages,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ]);

            $languageId = $request->language_id;
            $language = Language::find($languageId);
            if (!$language) return $this->errorResponse('Language not found', 404);

            try {
                $import = new Step5PageSettingImport($languageId);
                Excel::import($import, $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Step 5 page settings for {$language->name} uploaded successfully from Excel.");
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
        } catch (\Exception $e) {
            Log::error('Step5 Page Settings Excel upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Excel template for Step 5 page settings
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'single_column');
            $fileName = 'step5_page_settings_template_' . date('Y-m-d') . '.xlsx';
            return Excel::download(new Step5PageSettingTemplateExport($format), $fileName);
        } catch (\Exception $e) {
            Log::error('Step5 Page Settings template download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download template: ' . $e->getMessage(),
            ], 500);
        }
    }
}
