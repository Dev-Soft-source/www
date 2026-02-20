<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\BillingAddressSetting;
use App\Models\Language;
use App\Services\BillingAddressSettingService;
use App\Http\Resources\Admin\BillingAddressSettingResource;
use App\Imports\BillingAddressSettingImport;
use App\Exports\BillingAddressSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class BillingAdressSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $billingAddressSetting = BillingAddressSetting::with([
            'billingAddressSettingDetail', 
            'billingAddressSettingDetail.language:id,name'
        ])->first();

        // If no record exists, create one
        if (!$billingAddressSetting) {
            $billingAddressSetting = BillingAddressSetting::create([]);
            
            // Reload with relationships
            $billingAddressSetting = BillingAddressSetting::with([
                'billingAddressSettingDetail', 
                'billingAddressSettingDetail.language:id,name'
            ])->find($billingAddressSetting->id);
        }

        return $this->successResponse(
            new BillingAddressSettingResource($billingAddressSetting), 
            'Data Get Successfully!'
        );
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new BillingAddressSettingService();
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

        $billingAddressSettingDetail = BillingAddressSetting::first();
        if (!$billingAddressSettingDetail) {
            $billingAddressSettingDetail = BillingAddressSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($billingAddressSettingDetail, $language, $request);
        }

        if ($billingAddressSettingDetail) {
            return $this->successResponse([], "billing address page setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload billing address settings via Excel file.
     * Expects the all-languages template: Field Name column + one column per language. Updates billing_address_setting_detail for all languages.
     */
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
            ], [
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.file' => 'The uploaded file is not valid',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ]);

            try {
                $import = new BillingAddressSettingImport(null); // null = all-languages format
                Excel::import($import, $request->file('excel_file'));

                return $this->successResponse(
                    [],
                    'Billing address settings for all languages uploaded successfully from Excel.'
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
            Log::error('Excel upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Excel template for billing address settings.
     * format=all_languages: Field Name + one column per language (with current DB values if any). Other: single_column, multi_column.
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');

            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = BillingAddressSetting::with('billingAddressSettingDetail')->first();
            }

            $fileName = 'billing_address_settings_template_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new BillingAddressSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Template download error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to download template: ' . $e->getMessage(),
            ], 500);
        }
    }
}
