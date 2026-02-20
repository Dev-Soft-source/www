<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\PaymentSettingService;
use App\Http\Resources\Admin\PaymentOptionSettingResource;
use App\Imports\PaymentOptionSettingImport;
use App\Exports\PaymentOptionSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PaymentOptionSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $paymentSetting = PaymentSetting::with(['paymentSettingDetail', 'paymentSettingDetail.language:id,name'])->first();
        if (!$paymentSetting) {
            $paymentSetting = PaymentSetting::create([]);
            $paymentSetting = PaymentSetting::with(['paymentSettingDetail', 'paymentSettingDetail.language:id,name'])->find($paymentSetting->id);
        }
        
        return $this->successResponse(new PaymentOptionSettingResource($paymentSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new PaymentSettingService();
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

        $paymentSettingDetail = PaymentSetting::first();
        if (!$paymentSettingDetail) {
            $paymentSettingDetail = PaymentSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($paymentSettingDetail, $language, $request);
        }

        if ($paymentSettingDetail) {
            return $this->successResponse([], "Payment option setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload Payment Option settings via Excel (all-languages format: Field Name + one column per language).
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
                Excel::import(new PaymentOptionSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'Payment option settings for all languages uploaded successfully from Excel.'
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
            Log::error('Payment Option Excel upload error: ' . $e->getMessage());
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
                $existingData = PaymentSetting::with('paymentSettingDetail')->first();
            }
            return Excel::download(
                new PaymentOptionSettingTemplateExport($format, $languages, $existingData),
                'payment_option_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Payment Option template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
