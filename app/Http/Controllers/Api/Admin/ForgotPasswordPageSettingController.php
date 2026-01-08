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
                Excel::import(new ForgotPasswordPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Forgot password page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Forgot Password Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new ForgotPasswordPageSettingTemplateExport($request->get('format', 'single_column')),
                'forgot_password_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Forgot Password template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
