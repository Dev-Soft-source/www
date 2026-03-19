<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\ForTouristsPageSettingTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ForTouristsPageSettingResource;
use App\Imports\ForTouristsPageSettingImport;
use App\Models\ForTouristsPageSetting;
use App\Models\Language;
use App\Services\ForTouristsPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ForTouristsPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $forTouristsPageSetting = ForTouristsPageSetting::with(['forTouristsPageSettingDetail', 'forTouristsPageSettingDetail.language:id,name'])->first();
        if (!$forTouristsPageSetting) {
            $forTouristsPageSetting = ForTouristsPageSetting::create([]);
            $forTouristsPageSetting = ForTouristsPageSetting::with(['forTouristsPageSettingDetail', 'forTouristsPageSettingDetail.language:id,name'])->find($forTouristsPageSetting->id);
        }

        return $this->successResponse(new ForTouristsPageSettingResource($forTouristsPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ForTouristsPageSettingService();
        $response = $pageSettingService->validation($languages, $validationRule, $errorMessages);
        $validationRule = $response['validation_rules'];
        $errorMessages = $response['error_messages'];
        $niceNames = $response['nice_names'];

        $this->validate($request, $validationRule, $errorMessages, $niceNames);

        $forTouristsPageSetting = ForTouristsPageSetting::first();
        if (!$forTouristsPageSetting) {
            $forTouristsPageSetting = ForTouristsPageSetting::create([]);
        }

        foreach ($languages as $language) {
            $pageSettingService->update($forTouristsPageSetting, $language, $request);
        }

        if ($forTouristsPageSetting) {
            return $this->successResponse([], "For tourists page setting updated successfully.");
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
                Excel::import(new ForTouristsPageSettingImport(null), $request->file('excel_file'));

                return $this->successResponse(
                    [],
                    'For tourists page settings for all languages uploaded successfully from Excel.'
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
            Log::error('For Tourists Page Excel upload error: ' . $e->getMessage());
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
                $existingData = ForTouristsPageSetting::with('forTouristsPageSettingDetail')->first();
            }

            return Excel::download(
                new ForTouristsPageSettingTemplateExport($format, $languages, $existingData),
                'for_tourists_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('For Tourists Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
