<?php

namespace App\Imports;

use App\Models\Language;
use App\Models\SiteText;
use App\Models\SiteTextDetail;
use GPBMetadata\Google\Api\Log as ApiLog;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Log;
class SiteTextImport implements ToCollection, WithHeadingRow
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::orderBy('is_default', 'desc')->orderBy('id')->get();
    }

    protected function normalizeHeader(string $value): string
    {
        return trim(mb_strtolower(preg_replace('/\s+/', ' ', $value)));
    }

    protected function getRowValue($row, string ...$keys): string
    {
        foreach ($keys as $k) {
            $v = $row[$k] ?? null;
            if ($v !== null && $v !== '') {
                return trim((string) $v);
            }
        }
        return '';
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $first = $rows->first();
        $headers = array_keys($first->toArray());
        $defaultLang = $this->languages->first();

        $languageColumns = [];
        foreach ($headers as $h) {
            $normalized = $this->normalizeHeader(str_replace('_', ' ', (string) $h));
            if (in_array($normalized, ['no', 'slug'], true)) {
                continue;
            }
            if ($normalized === 'displaytext' || $normalized === 'display text') {
                if ($defaultLang) {
                    $languageColumns[] = ['key' => $h, 'language' => $defaultLang];
                }
                continue;
            }
            $lang = $this->languages->first(function ($l) use ($normalized) {
                return $this->normalizeHeader($l->name) === $normalized;
            });
            if ($lang) {
                $languageColumns[] = ['key' => $h, 'language' => $lang];
            }
        }

        foreach ($rows as $row) {
            $slug = trim((string) $this->getRowValue($row, 'slug', 'Slug'));
            if ($slug === '') {
                continue;
            }

            $displayText = $this->getRowValue($row, 'display text', 'display_text', 'Display Text');

            // Build one value per language so we only upsert once per (slug, language_id)
            $valuesByLanguage = [];
            foreach ($languageColumns as $col) {
                $langId = (int) $col['language']->id;
                $value = $this->getRowValue($row, $col['key']);
                if ($value === '' && $defaultLang && $langId === (int) $defaultLang->id && $displayText !== '') {
                    $value = $displayText;
                }
                if (!isset($valuesByLanguage[$langId]) || $value !== '') {
                    $valuesByLanguage[$langId] = $value;
                }
            }
            if ($displayText !== '' && $defaultLang) {
                $defaultId = (int) $defaultLang->id;
                if (!isset($valuesByLanguage[$defaultId]) || $valuesByLanguage[$defaultId] === '') {
                    $valuesByLanguage[$defaultId] = $displayText;
                }
            }

            foreach ($valuesByLanguage as $languageId => $value) {
                $this->upsertSiteText($slug, $languageId, (string) $value);
            }
        }
    }

    protected function upsertSiteText(string $slug, int $languageId, string $value): void
    {
        $slug = trim($slug);
        $languageId = (int) $languageId;

        $siteText = SiteText::where('slug', $slug)->first();
        $detail = SiteTextDetail::where('slug_id', $siteText->id)->where('language_id', $languageId)->first();
       
        if ($detail) {
            $detail->name = $value;
            $detail->save();
        } else {
            SiteTextDetail::create([
                'slug_id' => $siteText->id,
                'language_id' => $languageId,
                'name' => $value,
            ]);
        }
    }
}
