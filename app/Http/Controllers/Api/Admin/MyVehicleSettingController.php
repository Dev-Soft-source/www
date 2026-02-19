<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Models\MyVehicleSetting;
use App\Http\Controllers\Controller;
use App\Services\MyVehicleSettingService;
use App\Http\Resources\Admin\MyVehicleSettingResource;
use App\Imports\MyVehicleSettingImport;
use App\Exports\MyVehicleSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MyVehicleSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $myVehicleSetting = MyVehicleSetting::with(['myVehicleSettingDetail', 'myVehicleSettingDetail.language:id,name'])->first();
        if (!$myVehicleSetting) {
            $myVehicleSetting = MyVehicleSetting::create([]);
            $myVehicleSetting = MyVehicleSetting::with(['myVehicleSettingDetail', 'myVehicleSettingDetail.language:id,name'])->find($myVehicleSetting->id);
        }
        return $this->successResponse(new MyVehicleSettingResource($myVehicleSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MyVehicleSettingService();
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

        $myVehicleSettingDetail = MyVehicleSetting::first();
        if (!$myVehicleSettingDetail) {
            $myVehicleSettingDetail = MyVehicleSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($myVehicleSettingDetail, $language, $request);
        }

        if ($myVehicleSettingDetail) {
            return $this->successResponse([], "My Vehicle setting updated successfully.");
        }

        return $this->errorResponse();
    }

    /**
     * Upload My Vehicle settings via Excel (all-languages format: Field Name + one column per language).
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
                Excel::import(new MyVehicleSettingImport(null), $request->file('excel_file'));
                return $this->successResponse(
                    [],
                    'My Vehicle settings for all languages uploaded successfully from Excel.'
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
            Log::error('My Vehicle Excel upload error: ' . $e->getMessage());
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
                $existingData = MyVehicleSetting::with('myVehicleSettingDetail')->first();
            }
            return Excel::download(
                new MyVehicleSettingTemplateExport($format, $languages, $existingData),
                'my_vehicle_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('My Vehicle template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
