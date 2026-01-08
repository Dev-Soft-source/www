<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CoffeeWallPageSettingResource;
use App\Models\CoffeeWallPageSetting;
use App\Models\Language;
use App\Services\CoffeeWallSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\CoffeeWallPageSettingImport;
use App\Exports\CoffeeWallPageSettingTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class CoffeeWallPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $coffeeWallPageSetting = CoffeeWallPageSetting::with([
            'coffeeWallPageSettingDetail',
            'coffeeWallPageSettingDetail.language:id,name'
        ])->first();

        if (!$coffeeWallPageSetting) {
            $coffeeWallPageSetting = CoffeeWallPageSetting::create([]);
            $coffeeWallPageSetting = CoffeeWallPageSetting::with([
                'coffeeWallPageSettingDetail',
                'coffeeWallPageSettingDetail.language:id,name'
            ])->find($coffeeWallPageSetting->id);
        }

        return $this->successResponse(new CoffeeWallPageSettingResource($coffeeWallPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new CoffeeWallSettingService();
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

        $coffeeWallSetting = CoffeeWallPageSetting::first();
        if (!$coffeeWallSetting) {
            $coffeeWallSetting = CoffeeWallPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($coffeeWallSetting, $language, $request);
        }

        if ($coffeeWallSetting) {
            return $this->successResponse([], "Coffee wall setting updated successfully.");
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
                Excel::import(new CoffeeWallPageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(
                    ['language' => $language->name],
                    "Coffee wall settings for {$language->name} uploaded successfully from Excel."
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
            Log::error('Coffee Wall Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(
                new CoffeeWallPageSettingTemplateExport($request->get('format', 'single_column')),
                'coffee_wall_page_settings_template_' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Coffee Wall template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
