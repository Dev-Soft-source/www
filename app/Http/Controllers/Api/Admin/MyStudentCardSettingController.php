<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\MyStudentCardSetting;
use App\Services\MyStudentCardSettingService;
use App\Http\Resources\Admin\MyStudentCardSettingResource;
use App\Imports\MyStudentCardSettingImport;
use App\Exports\MyStudentCardSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyStudentCardSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myStudentCardSetting = MyStudentCardSetting::with(['myStudentSettingDetail', 'myStudentSettingDetail.language:id,name'])->first();
        if (!$myStudentCardSetting) {
            $myStudentCardSetting = MyStudentCardSetting::create([]);
            $myStudentCardSetting = MyStudentCardSetting::with(['myStudentSettingDetail', 'myStudentSettingDetail.language:id,name'])->find($myStudentCardSetting->id);
        }
        return $this->successResponse(new MyStudentCardSettingResource($myStudentCardSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyStudentCardSettingService();
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

        $StudentCardSettingDetail = MyStudentCardSetting::first();
        if (!$StudentCardSettingDetail) {
            $StudentCardSettingDetail = MyStudentCardSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($StudentCardSettingDetail, $language, $request);
        }

        if ($StudentCardSettingDetail) {
            return $this->successResponse([], "My Student Card setting updated successfully.");
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
                Excel::import(new MyStudentCardSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "My Student Card settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('My Student Card Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new MyStudentCardSettingTemplateExport($request->get('format', 'single_column')),
                'my_student_card_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('My Student Card template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
