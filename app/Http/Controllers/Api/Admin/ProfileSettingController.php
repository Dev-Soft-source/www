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

    /**
     * Upload profile settings via Excel file.
     * All-languages mode: no language_id, Excel has Field Name + one column per language.
     * Single-language mode: language_id required, Excel has one language column.
     */
    public function uploadExcel(Request $request)
    {
        try {
            $isAllLanguages = !$request->has('language_id') || $request->language_id === null || $request->language_id === '';

            if ($isAllLanguages) {
                $request->validate([
                    'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
                ], [
                    'excel_file.required' => 'Please upload an Excel file',
                    'excel_file.file' => 'The uploaded file is not valid',
                    'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                    'excel_file.max' => 'The file size must not exceed 5MB',
                ]);
            } else {
                $request->validate([
                    'language_id' => 'required|exists:languages,id',
                    'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
                ]);
            }

            $language = $isAllLanguages ? null : Language::find($request->language_id);
            if (!$isAllLanguages && !$language) {
                return $this->errorResponse('Language not found', 404);
            }

            try {
                Excel::import(new ProfileSettingImport($isAllLanguages ? null : $request->language_id), $request->file('excel_file'));

                if ($isAllLanguages) {
                    return $this->successResponse([], 'Profile settings for all languages uploaded successfully from Excel.');
                }
                return $this->successResponse(['language' => $language->name], "Profile settings for {$language->name} uploaded successfully from Excel.");
            } catch (ValidationException $e) {
                $failures = $e->failures();
                $errors = [];
                foreach ($failures as $failure) {
                    $errors[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                        'values' => $failure->values(),
                    ];
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => $errors,
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Profile Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    /**
     * Download Excel template. format=all_languages: Field Name + one column per language (with current DB values). Other: single_column.
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');

            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = ProfileSetting::with('profileSettingDetail')->first();
            }

            $fileName = 'profile_settings_template_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new ProfileSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Profile template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
