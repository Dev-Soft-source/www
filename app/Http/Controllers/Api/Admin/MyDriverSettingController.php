<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\DriverSetting;
use App\Models\Language;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\MyDriverSettingService;
use App\Http\Resources\Admin\MyDriverSettingResource;
use App\Imports\MyDriverSettingImport;
use App\Exports\MyDriverSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyDriverSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myDriverSetting = DriverSetting::with(['driverSettingDetail', 'driverSettingDetail.language:id,name'])->first();
        if (!$myDriverSetting) {
            $myDriverSetting = DriverSetting::create([]);
            $myDriverSetting = DriverSetting::with(['driverSettingDetail', 'driverSettingDetail.language:id,name'])->find($myDriverSetting->id);
        }
        return $this->successResponse(new MyDriverSettingResource($myDriverSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyDriverSettingService();
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

        $myDriverSettingDetail = DriverSetting::first();
        if (!$myDriverSettingDetail) {
            $myDriverSettingDetail = DriverSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myDriverSettingDetail, $language, $request);
        }

        if ($myDriverSettingDetail) {
            return $this->successResponse([], "My Driver License setting updated successfully.");
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
                Excel::import(new MyDriverSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Driver License settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('My Driver License Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyDriverSettingTemplateExport($request->get('format', 'single_column')),
                'my_driver_license_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Driver License template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
