<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\ContactProximaSettingService;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\ContactProximaRideSetting;
use App\Http\Resources\Admin\ContactUsSettingResource;
use App\Models\Language;
use App\Imports\ContactProximaRideSettingImport;
use App\Exports\ContactProximaRideSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ContactUsSettingController extends Controller
{
        use StatusResponser;

    public function show()
    {
        $contactUsSetting = ContactProximaRideSetting::with([
            'contactProximaRideSettingDetail',
            'contactProximaRideSettingDetail.language:id,name'
        ])->first();

        if (!$contactUsSetting) {
            $contactUsSetting = ContactProximaRideSetting::create([]);
            $contactUsSetting = ContactProximaRideSetting::with([
                'contactProximaRideSettingDetail',
                'contactProximaRideSettingDetail.language:id,name'
            ])->find($contactUsSetting->id);
        }

        return $this->successResponse(new ContactUsSettingResource($contactUsSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ContactProximaSettingService();
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

        $contactUsSettingDetail = ContactProximaRideSetting::first();
        if (!$contactUsSettingDetail) {
            $contactUsSettingDetail = ContactProximaRideSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($contactUsSettingDetail, $language, $request);
        }

        if ($contactUsSettingDetail) {
            return $this->successResponse([], "Profile page setting updated successfully.");
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
            if (!$language) {
                return $this->errorResponse('Language not found', 404);
            }

            try {
                Excel::import(new ContactProximaRideSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(
                    ['language' => $language->name],
                    "Contact Proxima settings for {$language->name} uploaded successfully from Excel."
                );
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
            Log::error('Contact Proxima Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(
                new ContactProximaRideSettingTemplateExport($request->get('format', 'single_column')),
                'contact_proxima_ride_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Contact Proxima template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
