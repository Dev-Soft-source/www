<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\DisclaimerPageSettingTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DisclaimerPageSettingResource;
use App\Imports\DisclaimerPageSettingImport;
use App\Models\DisclaimerPageSetting;
use App\Models\Language;
use App\Services\DisclaimerPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DisclaimerPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $disclaimerPageSetting = DisclaimerPageSetting::with(['disclaimerPageSettingDetail', 'disclaimerPageSettingDetail.language:id,name'])->first();
        if (!$disclaimerPageSetting) {
            $disclaimerPageSetting = DisclaimerPageSetting::create([]);
            $disclaimerPageSetting = DisclaimerPageSetting::with(['disclaimerPageSettingDetail', 'disclaimerPageSettingDetail.language:id,name'])->find($disclaimerPageSetting->id);
        }

        return $this->successResponse(new DisclaimerPageSettingResource($disclaimerPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new DisclaimerPageSettingService();
        $response = $pageSettingService->validation($languages, $validationRule, $errorMessages);
        $validationRule = $response['validation_rules'];
        $errorMessages = $response['error_messages'];
        $niceNames = $response['nice_names'];

        $this->validate($request, $validationRule, $errorMessages, $niceNames);

        $disclaimerPageSetting = DisclaimerPageSetting::first();
        if (!$disclaimerPageSetting) {
            $disclaimerPageSetting = DisclaimerPageSetting::create([]);
        }

        foreach ($languages as $language) {
            $pageSettingService->update($disclaimerPageSetting, $language, $request);
        }

        if ($disclaimerPageSetting) {
            return $this->successResponse([], "Disclaimer page setting updated successfully.");
        }

        return $this->errorResponse();
    }

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
                Excel::import(new DisclaimerPageSettingImport(null), $request->file('excel_file'));

                return $this->successResponse(
                    [],
                    'Disclaimer page settings for all languages uploaded successfully from Excel.'
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
            Log::error('Disclaimer Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');
            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = DisclaimerPageSetting::with('disclaimerPageSettingDetail')->first();
            }

            return Excel::download(
                new DisclaimerPageSettingTemplateExport($format, $languages, $existingData),
                'disclaimer_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Disclaimer Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
