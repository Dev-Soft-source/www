<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ForgotPasswordPageSettingResource;
use App\Models\ForgotPasswordPageSetting;
use App\Models\Language;
use App\Services\ForgotPasswordPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\ForgotPasswordPageSettingImport;
use App\Exports\ForgotPasswordPageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ForgotPasswordPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $forgotPasswordPageSetting = ForgotPasswordPageSetting::with(['forgotPasswordPageSettingDetail', 'forgotPasswordPageSettingDetail.language:id,name'])->first();
        if (!$forgotPasswordPageSetting) {
            $forgotPasswordPageSetting = ForgotPasswordPageSetting::create([]);
            $forgotPasswordPageSetting = ForgotPasswordPageSetting::with(['forgotPasswordPageSettingDetail', 'forgotPasswordPageSettingDetail.language:id,name'])->find($forgotPasswordPageSetting->id);
        }
        return $this->successResponse(new ForgotPasswordPageSettingResource($forgotPasswordPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ForgotPasswordPageSettingService();
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

        $forgotPasswordPageSetting = ForgotPasswordPageSetting::first();
        if (!$forgotPasswordPageSetting) {
            $forgotPasswordPageSetting = ForgotPasswordPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($forgotPasswordPageSetting, $language, $request);
        }

        if ($forgotPasswordPageSetting) {
            return $this->successResponse([], "Forgot password page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload forgot password page settings via Excel (all-languages format: Field Name + one column per language).
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
                Excel::import(new ForgotPasswordPageSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'Forgot password page settings for all languages uploaded successfully from Excel.'
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
            Log::error('Forgot Password Excel upload error: ' . $e->getMessage());
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
                $existingData = ForgotPasswordPageSetting::with('forgotPasswordPageSettingDetail')->first();
            }
            return Excel::download(
                new ForgotPasswordPageSettingTemplateExport($format, $languages, $existingData),
                'forgot_password_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Forgot Password template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
