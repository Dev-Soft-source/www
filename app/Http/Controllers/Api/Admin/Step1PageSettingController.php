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
                Excel::import(new Step1PageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Step 1 page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Step1 Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new Step1PageSettingTemplateExport($request->get('format', 'single_column')),
                'step1_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Step1 Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
