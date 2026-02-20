<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\SiteText;
use App\Models\SiteTextDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exports\SiteTextExport;
use App\Imports\SiteTextImport;

class SiteTextSettingController extends Controller
{
    use StatusResponser;

    /**
     * Return site texts grid: languages list and rows (No, slug, display_text, one column per language).
     * site_texts: one row per slug, .text = display text.
     * site_text_detail: slug_id -> site_texts.id, language_id, .name = translated text per language.
     */
    public function index()
    {
        $languages = Language::orderByRaw('is_default DESC')->orderBy('id')->get(['id', 'name', 'abbreviation']);
        $defaultLang = $languages->firstWhere('is_default', 1) ?: $languages->firstWhere('is_default', '1') ?: $languages->first();
        $defaultId = $defaultLang ? (int) $defaultLang->id : null;

        $siteTexts = SiteText::with(['details' => fn ($q) => $q->whereIn('language_id', $languages->pluck('id'))])
            ->orderBy('id')
            ->get();

        $rows = [];
        $no = 1;
        foreach ($siteTexts as $st) {
            $displayText = (string) ($st->text ?? '');
            $langValues = [];
            foreach ($st->details as $d) {
                $langValues[(int) $d->language_id] = (string) ($d->name ?? '');
            }
            foreach ($languages as $lang) {
                if (!array_key_exists($lang->id, $langValues)) {
                    $langValues[$lang->id] = '';
                }
            }
            if ($defaultId && isset($langValues[$defaultId]) && $langValues[$defaultId] !== '' && $displayText === '') {
                $displayText = $langValues[$defaultId];
            }
            $rows[] = [
                'id' => $st->id,
                'no' => $no++,
                'slug' => $st->slug,
                'display_text' => $displayText,
                'languages' => $langValues,
            ];
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Data retrieved successfully.',
            'data' => [
                'languages' => $languages,
                'rows' => $rows,
            ],
        ]);
    }

    /**
     * Export site texts to Excel (No, slug, display text, one column per language).
     */
    public function export(Request $request)
    {
        try {
            return Excel::download(
                new SiteTextExport(),
                'site_texts_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Site text export error: ' . $e->getMessage());
            return response()->json(['status' => 'Error', 'message' => 'Failed to export Excel'], 500);
        }
    }

    /**
     * Import site texts from Excel and update database.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ]);

            Excel::import(new SiteTextImport(), $request->file('excel_file'));

            return response()->json([
                'status' => 'Success',
                'message' => 'Site texts imported successfully.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Validation errors in Excel file',
                'errors' => array_map(fn ($f) => [
                    'row' => $f->row(),
                    'attribute' => $f->attribute(),
                    'errors' => $f->errors(),
                ], $e->failures()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Site text import error: ' . $e->getMessage());
            return response()->json(['status' => 'Error', 'message' => 'Failed to import Excel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Create a new site text (slug, display text, and per-language details).
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|max:191',
        ]);
        $slug = trim($request->slug);
        if (SiteText::where('slug', $slug)->exists()) {
            return response()->json(['status' => 'Error', 'message' => 'A site text with this slug already exists.'], 422);
        }
        $siteText = SiteText::create([
            'slug' => $slug,
            'text' => trim((string) $request->get('text', '')),
        ]);
        $languages = Language::orderByRaw('is_default DESC')->orderBy('id')->get(['id']);
        $langValues = $request->get('languages') ?? [];
        foreach ($languages as $lang) {
            $value = trim((string) ($langValues[$lang->id] ?? ''));
            SiteTextDetail::create([
                'slug_id' => $siteText->id,
                'language_id' => $lang->id,
                'name' => $value,
            ]);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Site text created successfully.',
            'data' => ['id' => $siteText->id],
        ]);
    }

    /**
     * Update an existing site text.
     */
    public function update(Request $request, $id)
    {
        $siteText = SiteText::findOrFail($id);
        $request->validate([
            'slug' => 'required|string|max:191',
        ]);
        $slug = trim($request->slug);
        if (SiteText::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return response()->json(['status' => 'Error', 'message' => 'A site text with this slug already exists.'], 422);
        }
        $siteText->slug = $slug;
        $siteText->text = trim((string) $request->get('text', ''));
        $siteText->save();
        $languages = $request->get('languages', []);
        foreach ($languages as $langId => $value) {
            $detail = SiteTextDetail::firstOrCreate(
                ['slug_id' => $siteText->id, 'language_id' => (int) $langId],
                ['name' => '']
            );
            $detail->name = trim((string) $value);
            $detail->save();
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Site text updated successfully.',
        ]);
    }

    /**
     * Delete a site text and its details.
     */
    public function destroy($id)
    {
        $siteText = SiteText::findOrFail($id);
        SiteTextDetail::where('slug_id', $siteText->id)->delete();
        $siteText->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Site text deleted successfully.',
        ]);
    }
}
