<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TermsOfUsePageSettingResource;
use App\Models\TermsOfUsePageSetting;
use App\Services\TermsOfUsePageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\TermsOfUsePageSettingImport;
use App\Exports\TermsOfUsePageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class TermsOfUsePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $termsOfUsePageSetting = TermsOfUsePageSetting::with(['termsOfUsePageSettingDetail', 'termsOfUsePageSettingDetail.language:id,name'])->first();
        if (!$termsOfUsePageSetting) {
            $termsOfUsePageSetting = TermsOfUsePageSetting::create([]);
            $termsOfUsePageSetting = TermsOfUsePageSetting::with(['termsOfUsePageSettingDetail', 'termsOfUsePageSettingDetail.language:id,name'])->find($termsOfUsePageSetting->id);
        }
        return $this->successResponse(new TermsOfUsePageSettingResource($termsOfUsePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new TermsOfUsePageSettingService();
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

        $termsOfUsePageSetting = TermsOfUsePageSetting::first();
        if (!$termsOfUsePageSetting) {
            $termsOfUsePageSetting = TermsOfUsePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($termsOfUsePageSetting, $language, $request);
        }

        if ($termsOfUsePageSetting) {
            return $this->successResponse([], "Terms of use page setting updated successfully.");
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
                Excel::import(new TermsOfUsePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Terms of Use page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Terms of Use Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new TermsOfUsePageSettingTemplateExport($request->get('format', 'single_column')),
                'terms_of_use_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Terms of Use template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
