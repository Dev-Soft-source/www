<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BookingPageSettingResource;
use App\Models\BookingPageSetting;
use App\Models\Language;
use App\Services\BookingPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\BookingPageSettingImport;
use App\Exports\BookingPageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class BookingPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $bookingPageSetting = BookingPageSetting::with([
            'bookingPageSettingDetail', 
            'bookingPageSettingDetail.language:id,name'
        ])->first();

        // If no record exists, create one
        if (!$bookingPageSetting) {
            $bookingPageSetting = BookingPageSetting::create([]);
            
            // Reload with relationships
            $bookingPageSetting = BookingPageSetting::with([
                'bookingPageSettingDetail', 
                'bookingPageSettingDetail.language:id,name'
            ])->find($bookingPageSetting->id);
        }

        return $this->successResponse(
            new BookingPageSettingResource($bookingPageSetting), 
            'Data Get Successfully!'
        );
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new BookingPageSettingService();
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

        $bookingPageSetting = BookingPageSetting::first();
        if (!$bookingPageSetting) {
            $bookingPageSetting = BookingPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($bookingPageSetting, $language, $request);
        }

        if ($bookingPageSetting) {
            return $this->successResponse([], "Booking page settings updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload booking page settings via Excel file
     */
    public function uploadExcel(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'language_id' => 'required|exists:languages,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ], [
                'language_id.required' => 'Please select a language',
                'language_id.exists' => 'Selected language does not exist',
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.file' => 'The uploaded file is not valid',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ]);

            $languageId = $request->language_id;
            $language = Language::find($languageId);

            if (!$language) {
                return $this->errorResponse('Language not found', 404);
            }

            // Import the Excel file
            try {
                $import = new BookingPageSettingImport($languageId);
                Excel::import($import, $request->file('excel_file'));

                return $this->successResponse(
                    ['language' => $language->name],
                    "Booking page settings for {$language->name} uploaded successfully from Excel."
                );
            } catch (ValidationException $e) {
                $failures = $e->failures();
                $errors = [];

                foreach ($failures as $failure) {
                    $errors[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                        'values' => $failure->values(),
                    ];
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => $errors,
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Booking Page Settings Excel upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Excel template for booking page settings
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'single_column');
            
            $fileName = 'booking_page_settings_template_' . date('Y-m-d') . '.xlsx';
            
            return Excel::download(
                new BookingPageSettingTemplateExport($format),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Booking Page Settings template download error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to download template: ' . $e->getMessage(),
            ], 500);
        }
    }
}
