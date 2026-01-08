<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DriverPageSettingResource;
use App\Models\DriverPageSetting;
use App\Models\Language;
use App\Services\DriverPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\DriverPageSettingImport;
use App\Exports\DriverPageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DriverPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $driverPageSetting = DriverPageSetting::with(['driverPageSettingDetail', 'driverPageSettingDetail.language:id,name'])->first();
        if (!$driverPageSetting) {
            $driverPageSetting = DriverPageSetting::create([]);
            $driverPageSetting = DriverPageSetting::with(['driverPageSettingDetail', 'driverPageSettingDetail.language:id,name'])->find($driverPageSetting->id);
        }
        return $this->successResponse(new DriverPageSettingResource($driverPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new DriverPageSettingService();
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

        $driverPageSetting = DriverPageSetting::first();
        if (!$driverPageSetting) {
            $driverPageSetting = DriverPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($driverPageSetting, $language, $request);
        }

        if ($driverPageSetting) {
            return $this->successResponse([], "Drivers page setting updated successfully.");
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
                Excel::import(new DriverPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Driver page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Driver Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new DriverPageSettingTemplateExport($request->get('format', 'single_column')),
                'driver_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Driver template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
