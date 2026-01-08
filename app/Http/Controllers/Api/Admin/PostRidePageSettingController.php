<?php

namespace App\Http\Controllers\Api\Admin;

use App\Imports\PostRidePageSettingImport;
use App\Exports\PostRidePageSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PostRidePageSettingResource;
use App\Models\PostRidePageSetting;
use App\Services\PostRidePageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class PostRidePageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $postRidePageSetting = PostRidePageSetting::with(['postRidePageSettingDetail','postRidePageSettingSubDetail','postRidePageSettingSubDetail.language:id,name', 'postRidePageSettingDetail.language:id,name'])->first();
        if (!$postRidePageSetting) {
            $postRidePageSetting = PostRidePageSetting::create([]);
            $postRidePageSetting = PostRidePageSetting::with(['postRidePageSettingDetail','postRidePageSettingSubDetail','postRidePageSettingSubDetail.language:id,name', 'postRidePageSettingDetail.language:id,name'])->find($postRidePageSetting->id);
        }
        return $this->successResponse(new PostRidePageSettingResource($postRidePageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new PostRidePageSettingService();
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

        $postRidePageSetting = PostRidePageSetting::first();
        if (!$postRidePageSetting) {
            $postRidePageSetting = PostRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($postRidePageSetting, $language, $request);
        }

        if ($postRidePageSetting) {
            return $this->successResponse([], "Post ride page setting updated successfully.");
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
                Excel::import(new PostRidePageSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Post Ride page settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Post Ride Page Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new PostRidePageSettingTemplateExport($request->get('format', 'single_column')),
                'post_ride_page_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Post Ride Page template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
