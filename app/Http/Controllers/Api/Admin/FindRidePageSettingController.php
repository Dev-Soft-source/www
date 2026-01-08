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
                Excel::import(new FindRidePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Find ride page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Find Ride Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new FindRidePageSettingTemplateExport($request->get('format', 'single_column')),
                'find_ride_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Find Ride template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
