<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\MyPhoneSetting;
use App\Models\MyReviewSetting;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Services\MyPhoneSettingService;
use App\Services\MyReviewSettingService;
use App\Http\Resources\Admin\MyReviewSettingResource;
use App\Imports\MyReviewSettingImport;
use App\Exports\MyReviewSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyReviewSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myReviewSetting = MyReviewSetting::with(['reviewSettingDetail', 'reviewSettingDetail.language:id,name'])->first();
        if (!$myReviewSetting) {
            $myReviewSetting = MyReviewSetting::create([]);
            $myReviewSetting = MyReviewSetting::with(['reviewSettingDetail', 'reviewSettingDetail.language:id,name'])->find($myReviewSetting->id);
        }
        return $this->successResponse(new MyReviewSettingResource($myReviewSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyReviewSettingService();
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

        $myReviewSettingDetail = MyReviewSetting::first();
        if (!$myReviewSettingDetail) {
            $myReviewSettingDetail = MyReviewSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myReviewSettingDetail, $language, $request);
        }

        if ($myReviewSettingDetail) {
            return $this->successResponse([], "My Review setting updated successfully.");
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
                Excel::import(new MyReviewSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Review settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('My Review Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyReviewSettingTemplateExport($request->get('format', 'single_column')),
                'my_review_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Review template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
