<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\Admin\MyEmailSettingResource;
use App\Models\MyEmailSetting;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\MyEmailSettingService;
use App\Imports\MyEmailSettingImport;
use App\Exports\MyEmailSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyEmailSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myEmailSetting = MyEmailSetting::with(['myEmailSettingDetail', 'myEmailSettingDetail.language:id,name'])->first();
        if (!$myEmailSetting) {
            $myEmailSetting = MyEmailSetting::create([]);
            $myEmailSetting = MyEmailSetting::with(['myEmailSettingDetail', 'myEmailSettingDetail.language:id,name'])->find($myEmailSetting->id);
        }
        return $this->successResponse(new MyEmailSettingResource($myEmailSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyEmailSettingService();
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

        $myEmailSettingDetail = MyEmailSetting::first();
        if (!$myEmailSettingDetail) {
            $myEmailSettingDetail = MyEmailSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myEmailSettingDetail, $language, $request);
        }

        if ($myEmailSettingDetail) {
            return $this->successResponse([], "My Email setting updated successfully.");
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
                Excel::import(new MyEmailSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Email settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('My Email Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyEmailSettingTemplateExport($request->get('format', 'single_column')),
                'my_email_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Email template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
