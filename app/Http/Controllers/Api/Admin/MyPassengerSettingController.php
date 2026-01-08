<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Models\MyPassengerSetting;
use App\Http\Controllers\Controller;
use App\Services\MyPassengerSettingService;
use App\Http\Resources\Admin\MyPassengerSettingResource;
use App\Imports\MyPassengerSettingImport;
use App\Exports\MyPassengerSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyPassengerSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myPassengerSetting = MyPassengerSetting::with(['myPassengerSettingDetail', 'myPassengerSettingDetail.language:id,name'])->first();
        if (!$myPassengerSetting) {
            $myPassengerSetting = MyPassengerSetting::create([]);
            $myPassengerSetting = MyPassengerSetting::with(['myPassengerSettingDetail', 'myPassengerSettingDetail.language:id,name'])->find($myPassengerSetting->id);
        }
        return $this->successResponse(new MyPassengerSettingResource($myPassengerSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyPassengerSettingService();
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

        $myPassengerSettingDetail = MyPassengerSetting::first();
        if (!$myPassengerSettingDetail) {
            $myPassengerSettingDetail = MyPassengerSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myPassengerSettingDetail, $language, $request);
        }

        if ($myPassengerSettingDetail) {
            return $this->successResponse([], "My Passenger setting updated successfully.");
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
                Excel::import(new MyPassengerSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Passenger settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('My Passenger Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyPassengerSettingTemplateExport($request->get('format', 'single_column')),
                'my_passenger_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Passenger template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
