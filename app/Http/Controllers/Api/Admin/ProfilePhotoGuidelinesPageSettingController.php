<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\ProfilePhotoGuidelinesPageSettingTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProfilePhotoGuidelinesPageSettingResource;
use App\Imports\ProfilePhotoGuidelinesPageSettingImport;
use App\Models\Language;
use App\Models\ProfilePhotoGuidelinesPageSetting;
use App\Services\ProfilePhotoGuidelinesPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ProfilePhotoGuidelinesPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $profilePhotoGuidelinesPageSetting = ProfilePhotoGuidelinesPageSetting::with(['profilePhotoGuidelinesPageSettingDetail', 'profilePhotoGuidelinesPageSettingDetail.language:id,name'])->first();
        if (! $profilePhotoGuidelinesPageSetting) {
            $profilePhotoGuidelinesPageSetting = ProfilePhotoGuidelinesPageSetting::create([]);
            $profilePhotoGuidelinesPageSetting = ProfilePhotoGuidelinesPageSetting::with(['profilePhotoGuidelinesPageSettingDetail', 'profilePhotoGuidelinesPageSettingDetail.language:id,name'])->find($profilePhotoGuidelinesPageSetting->id);
        }

        return $this->successResponse(new ProfilePhotoGuidelinesPageSettingResource($profilePhotoGuidelinesPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ProfilePhotoGuidelinesPageSettingService();
        $response = $pageSettingService->validation($languages, $validationRule, $errorMessages);
        $validationRule = $response['validation_rules'];
        $errorMessages = $response['error_messages'];
        $niceNames = $response['nice_names'];

        $this->validate($request, $validationRule, $errorMessages, $niceNames);

        $profilePhotoGuidelinesPageSetting = ProfilePhotoGuidelinesPageSetting::first();
        if (! $profilePhotoGuidelinesPageSetting) {
            $profilePhotoGuidelinesPageSetting = ProfilePhotoGuidelinesPageSetting::create([]);
        }

        foreach ($languages as $language) {
            $pageSettingService->update($profilePhotoGuidelinesPageSetting, $language, $request);
        }

        if ($profilePhotoGuidelinesPageSetting) {
            return $this->successResponse([], "Profile photo guidelines page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ], [
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.file' => 'The uploaded file is not valid',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ]);

            try {
                Excel::import(new ProfilePhotoGuidelinesPageSettingImport(null), $request->file('excel_file'));
                return $this->successResponse([], 'Profile photo guidelines page settings for all languages uploaded successfully from Excel.');
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => array_map(fn ($f) => [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors(),
                    ], $e->failures()),
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Profile Photo Guidelines Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');
            $languages = null;
            $existingData = null;

            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = ProfilePhotoGuidelinesPageSetting::with('profilePhotoGuidelinesPageSettingDetail')->first();
            }

            return Excel::download(
                new ProfilePhotoGuidelinesPageSettingTemplateExport($format, $languages, $existingData),
                'profile_photo_guidelines_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Profile Photo Guidelines Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
