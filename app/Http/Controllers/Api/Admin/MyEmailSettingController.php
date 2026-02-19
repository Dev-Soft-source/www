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

    /**
     * Upload my email settings via Excel (all-languages format: Field Name + one column per language).
     */
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
                Excel::import(new MyEmailSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'My Email settings for all languages uploaded successfully from Excel.'
                );
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
            Log::error('My Email Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download Excel template. format=all_languages (default): Field Name + one column per language.
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');
            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = MyEmailSetting::with('myEmailSettingDetail')->first();
            }
            return Excel::download(
                new MyEmailSettingTemplateExport($format, $languages, $existingData),
                'my_email_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('My Email template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
