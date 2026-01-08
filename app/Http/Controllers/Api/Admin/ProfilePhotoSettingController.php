<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Models\ProfilePhotoSetting;
use App\Http\Controllers\Controller;
use App\Services\ProfilePhotoSettingService;
use App\Http\Resources\Admin\ProfilePhotoSettingResource;
use App\Imports\ProfilePhotoSettingImport;
use App\Exports\ProfilePhotoSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ProfilePhotoSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $profilePageSetting = ProfilePhotoSetting::with(['profilePhotoSettingDetail', 'profilePhotoSettingDetail.language:id,name'])->first();
        if (!$profilePageSetting) {
            $profilePageSetting = ProfilePhotoSetting::create([]);
            $profilePageSetting = ProfilePhotoSetting::with(['profilePhotoSettingDetail', 'profilePhotoSettingDetail.language:id,name'])->find($profilePageSetting->id);
        }
        return $this->successResponse(new ProfilePhotoSettingResource($profilePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ProfilePhotoSettingService();
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

        $profilePhotoSettingDetail = ProfilePhotoSetting::first();
        if (!$profilePhotoSettingDetail) {
            $profilePhotoSettingDetail = ProfilePhotoSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($profilePhotoSettingDetail, $language, $request);
        }

        if ($profilePhotoSettingDetail) {
            return $this->successResponse([], "Profile photo setting updated successfully.");
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
                Excel::import(new ProfilePhotoSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Profile photo settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Profile Photo Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new ProfilePhotoSettingTemplateExport($request->get('format', 'single_column')),
                'profile_photo_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Profile Photo template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
