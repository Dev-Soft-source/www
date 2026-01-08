<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Step2PageSettingResource;
use App\Models\Step2PageSetting;
use App\Services\Step2PageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\Step2PageSettingImport;
use App\Exports\Step2PageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class Step2PageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $step2PageSetting = Step2PageSetting::with(['step2PageSettingDetail', 'step2PageSettingDetail.language:id,name'])->first();
        if (!$step2PageSetting) {
            $step2PageSetting = Step2PageSetting::create([]);
            $step2PageSetting = Step2PageSetting::with(['step2PageSettingDetail', 'step2PageSettingDetail.language:id,name'])->find($step2PageSetting->id);
        }
        return $this->successResponse(new Step2PageSettingResource($step2PageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new Step2PageSettingService();
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

        $step2PageSetting = Step2PageSetting::first();
        if (!$step2PageSetting) {
            $step2PageSetting = Step2PageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($step2PageSetting, $language, $request);
        }

        if ($step2PageSetting) {
            return $this->successResponse([], "Step 2 of 4 page setting updated successfully.");
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
                Excel::import(new Step2PageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Step 2 page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Step2 Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new Step2PageSettingTemplateExport($request->get('format', 'single_column')),
                'step2_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Step2 Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
