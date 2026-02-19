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

    /**
     * Upload contact ProximaRide settings via Excel (all-languages format: Field Name + one column per language).
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
                Excel::import(new ContactProximaRideSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'Contact ProximaRide settings for all languages uploaded successfully from Excel.'
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
            Log::error('Contact Proxima Excel upload error: ' . $e->getMessage());
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
                $existingData = ContactProximaRideSetting::with('contactProximaRideSettingDetail')->first();
            }
            return Excel::download(
                new ContactProximaRideSettingTemplateExport($format, $languages, $existingData),
                'contact_proxima_ride_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Contact Proxima template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
