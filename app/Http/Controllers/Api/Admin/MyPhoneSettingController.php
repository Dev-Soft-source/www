<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MyPhoneSettingResource;
use App\Models\MyPhoneSetting;
use App\Services\MyPhoneSettingService;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Imports\MyPhoneSettingImport;
use App\Exports\MyPhoneSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyPhoneSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myPhoneSetting = MyPhoneSetting::with(['myPhoneSettingDetail', 'myPhoneSettingDetail.language:id,name'])->first();
        if (!$myPhoneSetting) {
            $myPhoneSetting = MyPhoneSetting::create([]);
            $myPhoneSetting = MyPhoneSetting::with(['myPhoneSettingDetail', 'myPhoneSettingDetail.language:id,name'])->find($myPhoneSetting->id);
        }
        return $this->successResponse(new MyPhoneSettingResource($myPhoneSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyPhoneSettingService();
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

        $myPhoneSettingDetail = MyPhoneSetting::first();
        if (!$myPhoneSettingDetail) {
            $myPhoneSettingDetail = MyPhoneSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myPhoneSettingDetail, $language, $request);
        }

        if ($myPhoneSettingDetail) {
            return $this->successResponse([], "My Phone setting updated successfully.");
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
                Excel::import(new MyPhoneSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Phone settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('My Phone Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyPhoneSettingTemplateExport($request->get('format', 'single_column')),
                'my_phone_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Phone template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
