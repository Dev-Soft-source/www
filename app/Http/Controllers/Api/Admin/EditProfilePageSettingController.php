<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\EditProfilePageSetting;
use App\Services\EditProfileSettingService;
use App\Http\Resources\Admin\EditProfilePageSettingResource;
use App\Models\Language;
use App\Imports\EditProfilePageSettingImport;
use App\Exports\EditProfilePageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class EditProfilePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $editProfilePageSetting = EditProfilePageSetting::with(['editProfilePageSettingDetail', 'editProfilePageSettingDetail.language:id,name'])->first();
        if (!$editProfilePageSetting) {
            $editProfilePageSetting = EditProfilePageSetting::create([]);
            $editProfilePageSetting = EditProfilePageSetting::with(['editProfilePageSettingDetail', 'editProfilePageSettingDetail.language:id,name'])->find($editProfilePageSetting->id);
        }
        return $this->successResponse(new EditProfilePageSettingResource($editProfilePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new EditProfileSettingService();
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

        $editProfilePageSetting = EditProfilePageSetting::first();
        if (!$editProfilePageSetting) {
            $editProfilePageSetting = EditProfilePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($editProfilePageSetting, $language, $request);
        }

        if ($editProfilePageSetting) {
            return $this->successResponse([], "Edit profile page setting updated successfully.");
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
                Excel::import(new EditProfilePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Edit profile page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Edit Profile Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new EditProfilePageSettingTemplateExport($request->get('format', 'single_column')),
                'edit_profile_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Edit Profile template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
