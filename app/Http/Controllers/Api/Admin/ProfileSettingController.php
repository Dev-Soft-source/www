<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\ProfileSetting;
use App\Traits\StatusResponser;
use App\Models\ProfilePhotoSetting;
use App\Http\Controllers\Controller;
use App\Services\ProfileSettingService;
use App\Http\Resources\Admin\ProfileSettingResource;
use App\Imports\ProfileSettingImport;
use App\Exports\ProfileSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ProfileSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $profileSetting = ProfileSetting::with(['profileSettingDetail', 'profileSettingDetail.language:id,name'])->first();
        if (!$profileSetting) {
            $profileSetting = ProfileSetting::create([]);
            $profileSetting = ProfileSetting::with(['profileSettingDetail', 'profileSettingDetail.language:id,name'])->find($profileSetting->id);
        }
        return $this->successResponse(new ProfileSettingResource($profileSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ProfileSettingService();
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

        $profileSettingDetail = ProfileSetting::first();
        if (!$profileSettingDetail) {
            $profileSettingDetail = ProfileSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($profileSettingDetail, $language, $request);
        }

        if ($profileSettingDetail) {
            return $this->successResponse([], "Profile setting updated successfully.");
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
                Excel::import(new ProfileSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Profile settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Profile Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new ProfileSettingTemplateExport($request->get('format', 'single_column')),
                'profile_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Profile template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
