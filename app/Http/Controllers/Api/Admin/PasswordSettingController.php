<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\PasswordSetting;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\PasswordSettingService;
use App\Http\Resources\Admin\PasswordSettingResource;
use App\Imports\PasswordSettingImport;
use App\Exports\PasswordSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PasswordSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $passwordSetting = PasswordSetting::with(['passwordSettingDetail', 'passwordSettingDetail.language:id,name'])->first();
        if (!$passwordSetting) {
            $passwordSetting = PasswordSetting::create([]);
            $passwordSetting = PasswordSetting::with(['passwordSettingDetail', 'passwordSettingDetail.language:id,name'])->find($passwordSetting->id);
        }
        return $this->successResponse(new PasswordSettingResource($passwordSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new PasswordSettingService();
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

        $passwordSettingDetail = PasswordSetting::first();
        if (!$passwordSettingDetail) {
            $passwordSettingDetail = PasswordSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($passwordSettingDetail, $language, $request);
        }

        if ($passwordSettingDetail) {
            return $this->successResponse([], "Password setting updated successfully.");
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
                Excel::import(new PasswordSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Password settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Password Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new PasswordSettingTemplateExport($request->get('format', 'single_column')),
                'password_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Password Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
