<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\SiteText;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Export uses: site_texts (one per slug, .text = display text),
 * site_text_detail (slug_id -> site_texts.id, language_id, .name = value per language).
 */
class SiteTextExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection(): Collection
    {
        $languages = Language::orderByRaw('is_default DESC')->orderBy('id')->get(['id', 'name']);
        $defaultLang = $languages->firstWhere('is_default', 1) ?: $languages->firstWhere('is_default', '1') ?: $languages->first();
        $defaultId = $defaultLang ? (int) $defaultLang->id : null;

        $siteTexts = SiteText::with(['details' => fn ($q) => $q->whereIn('language_id', $languages->pluck('id'))])
            ->orderBy('id')
            ->get();

        $rows = [];
        $no = 1;
        foreach ($siteTexts as $st) {
            $displayText = (string) ($st->text ?? '');
            $detailsByLang = $st->details->keyBy('language_id');
            $row = [$no++, $st->slug, $displayText];
            foreach ($languages as $lang) {
                $d = $detailsByLang->get($lang->id);
                $row[] = $d ? (string) ($d->name ?? '') : '';
            }
            if ($defaultId && ($displayText === '') && $detailsByLang->has($defaultId)) {
                $row[2] = (string) ($detailsByLang->get($defaultId)->name ?? '');
            }
            $rows[] = $row;
        }

        return new Collection($rows);
    }

    public function headings(): array
    {
        $languages = Language::orderByRaw('is_default DESC')->orderBy('id')->get(['id', 'name']);
        $headings = ['No', 'slug', 'display text'];
        foreach ($languages as $lang) {
            $headings[] = $lang->name;
        }
        return $headings;
    }
}
