<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Models\ProfilePageSetting;
use App\Http\Controllers\Controller;
use App\Services\ProfilePageSettingService;
use App\Http\Resources\Admin\ProfilePageSettingResource;
use App\Imports\ProfilePageSettingImport;
use App\Exports\ProfilePageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ProfilePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $profilePageSetting = ProfilePageSetting::with(['profilePageSettingDetail', 'profilePageSettingDetail.language:id,name'])->first();
        if (!$profilePageSetting) {
            $profilePageSetting = ProfilePageSetting::create([]);
            $profilePageSetting = ProfilePageSetting::with(['profilePageSettingDetail', 'profilePageSettingDetail.language:id,name'])->find($profilePageSetting->id);
        }
        return $this->successResponse(new ProfilePageSettingResource($profilePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ProfilePageSettingService();
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

        $profilePageSetting = ProfilePageSetting::first();
        if (!$profilePageSetting) {
            $profilePageSetting = ProfilePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($profilePageSetting, $language, $request);
        }

        if ($profilePageSetting) {
            return $this->successResponse([], "Profile page setting updated successfully.");
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
                Excel::import(new ProfilePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Profile page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Profile Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new ProfilePageSettingTemplateExport($request->get('format', 'single_column')),
                'profile_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Profile Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
