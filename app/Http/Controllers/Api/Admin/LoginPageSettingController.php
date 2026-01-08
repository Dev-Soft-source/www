<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LoginPageSettingResource;
use App\Models\LoginPageSetting;
use App\Models\Language;
use App\Services\LoginPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\LoginPageSettingImport;
use App\Exports\LoginPageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class LoginPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $loginPageSetting = LoginPageSetting::with(['loginPageSettingDetail', 'loginPageSettingDetail.language:id,name'])->first();
        if (!$loginPageSetting) {
            $loginPageSetting = LoginPageSetting::create([]);
            $loginPageSetting = LoginPageSetting::with(['loginPageSettingDetail', 'loginPageSettingDetail.language:id,name'])->find($loginPageSetting->id);
        }
        return $this->successResponse(new LoginPageSettingResource($loginPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new LoginPageSettingService();
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

        $loginPageSetting = LoginPageSetting::first();
        if (!$loginPageSetting) {
            $loginPageSetting = LoginPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($loginPageSetting, $language, $request);
        }

        if ($loginPageSetting) {
            return $this->successResponse([], "Login page setting updated successfully.");
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
                Excel::import(new LoginPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Login page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Login Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new LoginPageSettingTemplateExport($request->get('format', 'single_column')),
                'login_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Login Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
