<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\CloseAccountSettingService;
use Illuminate\Http\Request;
use App\Models\CloseAccountSetting;
use App\Models\Language;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CloseAccountSettingResource;
use App\Traits\StatusResponser;
use App\Imports\CloseAccountSettingImport;
use App\Exports\CloseAccountSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class CloseAccountSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $closeAccountSetting = CloseAccountSetting::with([
            'closeAccountSettingDetail', 
            'closeAccountSettingDetail.language:id,name'
        ])->first();

        if (!$closeAccountSetting) {
            $closeAccountSetting = CloseAccountSetting::create([]);
            $closeAccountSetting = CloseAccountSetting::with([
                'closeAccountSettingDetail', 
                'closeAccountSettingDetail.language:id,name'
            ])->find($closeAccountSetting->id);
        }

        return $this->successResponse(
            new CloseAccountSettingResource($closeAccountSetting), 
            'Data Get Successfully!'
        );
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new CloseAccountSettingService();
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

        $closeAccountSettingDetail = CloseAccountSetting::first();
        if (!$closeAccountSettingDetail) {
            $closeAccountSettingDetail = CloseAccountSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($closeAccountSettingDetail, $language, $request);
        }

        if ($closeAccountSettingDetail) {
            return $this->successResponse([], "Close account page setting updated successfully.");
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
                Excel::import(new CloseAccountSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(
                    ['language' => $language->name],
                    "Close account settings for {$language->name} uploaded successfully from Excel."
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
            Log::error('Close Account Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(
                new CloseAccountSettingTemplateExport($request->get('format', 'single_column')),
                'close_account_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Close Account template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
