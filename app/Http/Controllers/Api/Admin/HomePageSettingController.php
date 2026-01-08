<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\HomePageSettingResource;
use App\Models\HomePageSetting;
use App\Models\Language;
use App\Services\HomePageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\HomePageSettingImport;
use App\Exports\HomePageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class HomePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $homePageSetting = HomePageSetting::with(['homePageSettingDetail', 'homePageSettingDetail.language:id,name'])->first();
        if (!$homePageSetting) {
            $homePageSetting = HomePageSetting::create([]);
            $homePageSetting = HomePageSetting::with(['homePageSettingDetail', 'homePageSettingDetail.language:id,name'])->find($homePageSetting->id);
        }
        return $this->successResponse(new HomePageSettingResource($homePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new HomePageSettingService();
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

        $homePageSetting = HomePageSetting::first();
        if (!$homePageSetting) {
            $homePageSetting = HomePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($homePageSetting, $language, $request);
        }

        if ($homePageSetting) {
            return $this->successResponse([], "Home page setting updated successfully.");
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
                Excel::import(new HomePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Home page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Home Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new HomePageSettingTemplateExport($request->get('format', 'single_column')),
                'home_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Home Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
