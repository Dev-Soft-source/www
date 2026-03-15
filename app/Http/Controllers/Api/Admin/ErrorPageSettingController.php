<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ErrorPageSettingResource;
use App\Models\ErrorPageSetting;
use App\Services\ErrorPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\ErrorPageSettingImport;
use App\Exports\ErrorPageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ErrorPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $setting = ErrorPageSetting::with(['errorPageSettingDetail', 'errorPageSettingDetail.language:id,name'])->first();
        if (!$setting) {
            $setting = ErrorPageSetting::create([]);
            $setting = ErrorPageSetting::with(['errorPageSettingDetail', 'errorPageSettingDetail.language:id,name'])->find($setting->id);
        }
        return $this->successResponse(new ErrorPageSettingResource($setting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $languages = getAllLanguages();
        $service = new ErrorPageSettingService();
        $setting = ErrorPageSetting::first();
        if (!$setting) {
            $setting = ErrorPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $service->update($setting, $language, $request);
        }
        return $this->successResponse([], 'Error page setting updated successfully.');
    }

    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ], [
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ]);
            try {
                Excel::import(new ErrorPageSettingImport(null), $request->file('excel_file'));
                return $this->successResponse([], 'Error page settings uploaded successfully.');
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
            Log::error('Error page Excel upload: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');
            $languages = $format === 'all_languages' ? Language::orderBy('id')->get() : null;
            $existingData = ErrorPageSetting::with('errorPageSettingDetail')->first();
            return Excel::download(
                new ErrorPageSettingTemplateExport($format, $languages, $existingData),
                'error_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Error page template download: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
