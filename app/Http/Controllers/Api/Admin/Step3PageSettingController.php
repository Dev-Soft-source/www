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
                Excel::import(new Step3PageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Step 3 page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Step3 Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new Step3PageSettingTemplateExport($request->get('format', 'single_column')),
                'step3_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Step3 Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
