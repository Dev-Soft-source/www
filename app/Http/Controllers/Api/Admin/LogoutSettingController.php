<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\LogoutSetting;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\LogoutSettingService;
use App\Http\Resources\Admin\LogoutSettingResource;
use App\Models\Language;
use App\Imports\LogoutSettingImport;
use App\Exports\LogoutSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class LogoutSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $logoutSetting = LogoutSetting::with(['logoutSettingDetail', 'logoutSettingDetail.language:id,name'])->first();
        if (!$logoutSetting) {
            $logoutSetting = LogoutSetting::create([]);
            $logoutSetting = LogoutSetting::with(['logoutSettingDetail', 'logoutSettingDetail.language:id,name'])->find($logoutSetting->id);
        }
        return $this->successResponse(new LogoutSettingResource($logoutSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new LogoutSettingService();
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

        $logoutSettingDetail = LogoutSetting::first();
        if (!$logoutSettingDetail) {
            $logoutSettingDetail = LogoutSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($logoutSettingDetail, $language, $request);
        }

        if ($logoutSettingDetail) {
            return $this->successResponse([], "Logout page setting updated successfully.");
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
                Excel::import(new LogoutSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Logout settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Logout Setting Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new LogoutSettingTemplateExport($request->get('format', 'single_column')),
                'logout_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Logout Setting template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
