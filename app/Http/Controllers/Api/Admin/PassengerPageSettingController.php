<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PassengerPageSettingResource;
use App\Models\PassengerPageSetting;
use App\Services\PassengerPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\PassengerPageSettingImport;
use App\Exports\PassengerPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PassengerPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $passengerPageSetting = PassengerPageSetting::with(['passengerPageSettingDetail', 'passengerPageSettingDetail.language:id,name'])->first();
        if (!$passengerPageSetting) {
            $passengerPageSetting = PassengerPageSetting::create([]);
            $passengerPageSetting = PassengerPageSetting::with(['passengerPageSettingDetail', 'passengerPageSettingDetail.language:id,name'])->find($passengerPageSetting->id);
        }
        return $this->successResponse(new PassengerPageSettingResource($passengerPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new PassengerPageSettingService();
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

        $passengerPageSetting = PassengerPageSetting::first();
        if (!$passengerPageSetting) {
            $passengerPageSetting = PassengerPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($passengerPageSetting, $language, $request);
        }

        if ($passengerPageSetting) {
            return $this->successResponse([], "Passengers page setting updated successfully.");
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
                Excel::import(new PassengerPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Passenger page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Passenger Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new PassengerPageSettingTemplateExport($request->get('format', 'single_column')),
                'passenger_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Passenger Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
