<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\FindRidePageSettingResource;
use App\Models\FindRidePageSetting;
use App\Models\Language;
use App\Services\FindRidePageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\FindRidePageSettingImport;
use App\Exports\FindRidePageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class FindRidePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $findRidePageSetting = FindRidePageSetting::with(['findRidePageSettingDetail', 'findRidePageSettingDetail.language:id,name'])->first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = FindRidePageSetting::create([]);
            $findRidePageSetting = FindRidePageSetting::with(['findRidePageSettingDetail', 'findRidePageSettingDetail.language:id,name'])->find($findRidePageSetting->id);
        }
        return $this->successResponse(new FindRidePageSettingResource($findRidePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();        
        $pageSettingService = new FindRidePageSettingService();
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

        $findRidePageSetting = FindRidePageSetting::first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = FindRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($findRidePageSetting, $language, $request);
        }

        if ($findRidePageSetting) {
            return $this->successResponse([], "Find ride page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload find ride page settings via Excel (all-languages format: Field Name + one column per language).
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
                Excel::import(new FindRidePageSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'Find ride page settings for all languages uploaded successfully from Excel.'
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
            Log::error('Find Ride Excel upload error: ' . $e->getMessage());
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
                $existingData = FindRidePageSetting::with('findRidePageSettingDetail')->first();
            }
            return Excel::download(
                new FindRidePageSettingTemplateExport($format, $languages, $existingData),
                'find_ride_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Find Ride template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
