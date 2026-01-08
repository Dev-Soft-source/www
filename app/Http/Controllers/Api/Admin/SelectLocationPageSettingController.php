<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SelectLocationPageSettingResource;
use App\Models\SelectLocationSetting;
use App\Services\SelectLocationPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\SelectLocationPageSettingImport;
use App\Exports\SelectLocationPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class SelectLocationPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $selectLocationPageSetting = SelectLocationSetting::with(['selectLocationSettingDetail', 'selectLocationSettingDetail.language:id,name'])->first();
        if (!$selectLocationPageSetting) {
            $selectLocationPageSetting = SelectLocationSetting::create([]);
            $selectLocationPageSetting = SelectLocationSetting::with(['selectLocationSettingDetail', 'selectLocationSettingDetail.language:id,name'])->find($selectLocationPageSetting->id);
        }
        return $this->successResponse(new SelectLocationPageSettingResource($selectLocationPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new SelectLocationPageSettingService();
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

        $selectLocationSetting = SelectLocationSetting::first();
        if (!$selectLocationSetting) {
            $selectLocationSetting = SelectLocationSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($selectLocationSetting, $language, $request);
        }

        if ($selectLocationSetting) {
            return $this->successResponse([], "Page settings updated successfully.");
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
                Excel::import(new SelectLocationPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Select Location page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Select Location Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new SelectLocationPageSettingTemplateExport($request->get('format', 'single_column')),
                'select_location_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Select Location Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
