<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DisputePageSettingResource;
use App\Models\DisputePageSetting;
use App\Models\Language;
use App\Services\DisputePageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\DisputePageSettingImport;
use App\Exports\DisputePageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DisputePolicyPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $disputePageSetting = DisputePageSetting::with(['disputePageSettingDetail', 'disputePageSettingDetail.language:id,name'])->first();
        if (!$disputePageSetting) {
            $disputePageSetting = DisputePageSetting::create([]);
            $disputePageSetting = DisputePageSetting::with(['disputePageSettingDetail', 'disputePageSettingDetail.language:id,name'])->find($disputePageSetting->id);
        }
        return $this->successResponse(new DisputePageSettingResource($disputePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new DisputePageSettingService();
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

        $disputePageSetting = DisputePageSetting::first();
        if (!$disputePageSetting) {
            $disputePageSetting = DisputePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($disputePageSetting, $language, $request);
        }

        if ($disputePageSetting) {
            return $this->successResponse([], "Dispute page setting updated successfully.");
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
                Excel::import(new DisputePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Dispute page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Dispute Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new DisputePageSettingTemplateExport($request->get('format', 'single_column')), 'dispute_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Dispute template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
