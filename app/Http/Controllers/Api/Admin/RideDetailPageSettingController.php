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

    /**
     * Upload Ride Detail (Trip details) page settings via Excel.
     * When language_id is present: single-language format. When absent: all_languages format (Field Name + one column per language).
     */
    public function uploadExcel(Request $request)
    {
        try {
            $rules = [
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ];
            $messages = [
                'excel_file.required' => 'Please upload an Excel file',
                'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
                'excel_file.max' => 'The file size must not exceed 5MB',
            ];
            if ($request->has('language_id')) {
                $rules['language_id'] = 'required|exists:languages,id';
                $messages['language_id.required'] = 'Please select a language';
                $messages['language_id.exists'] = 'Selected language does not exist';
            }
            $request->validate($rules, $messages);

            $languageId = $request->input('language_id');
            $import = new RideDetailPageSettingImport($languageId);

            try {
                Excel::import($import, $request->file('excel_file'));

                if ($languageId) {
                    $language = Language::find($languageId);
                    return $this->successResponse(
                        ['language' => $language->name],
                        "Ride detail page settings for {$language->name} uploaded successfully from Excel."
                    );
                }
                return $this->successResponse(
                    [],
                    'Trip details page settings for all languages uploaded successfully from Excel.'
                );
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => array_map(fn($f) => [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors(),
                        'values' => $f->values(),
                    ], $e->failures()),
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Ride Detail Setting Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    /**
     * Download Excel template for Ride Detail page settings.
     * format=all_languages: Field Name + one column per language (with current DB values if any).
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'all_languages');

            $languages = null;
            $existingData = null;
            if ($format === 'all_languages') {
                $languages = Language::orderBy('id')->get();
                $existingData = RideDetailPageSetting::with('rideDetailPageSettingDetail')->first();
            }

            $fileName = 'ride_detail_page_setting_template_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new RideDetailPageSettingTemplateExport($format, $languages, $existingData),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Ride Detail template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
