<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RideDetailPageSettingResource;
use App\Models\RideDetailPageSetting;
use App\Services\RideDetailPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Imports\RideDetailPageSettingImport;
use App\Exports\RideDetailPageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class RideDetailPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $rideDetailPageSetting = RideDetailPageSetting::query();

        // $defaultLang = getDefaultLanguage();
        // $rideDetailPageSetting = $rideDetailPageSetting->with(['rideDetailPageSettingDetail' => function ($q) use ($defaultLang) {
        //     $q->where('language_id', $defaultLang->id);
        // }]);
        
        $rideDetailPageSetting = $rideDetailPageSetting->with(['rideDetailPageSettingDetail', 'rideDetailPageSettingDetail.language:id,name']);
        $rideDetailPageSetting = $rideDetailPageSetting->first();

        return $this->successResponse($rideDetailPageSetting ? new RideDetailPageSettingResource($rideDetailPageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new RideDetailPageSettingService();
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

        $rideDetailPageSetting = RideDetailPageSetting::first();
        if (!$rideDetailPageSetting) {
            $rideDetailPageSetting = RideDetailPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($rideDetailPageSetting, $language, $request);
        }

        if ($rideDetailPageSetting) {
            return $this->successResponse([], "Trip detail page setting updated successfully.");
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
                Excel::import(new RideDetailPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Ride detail page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Ride Detail Setting Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        $format = $request->get('format', 'single_column');
        return Excel::download(new RideDetailPageSettingTemplateExport($format), 'ride_detail_page_setting_template.xlsx');
    }
}
