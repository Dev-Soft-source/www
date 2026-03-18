<?php

namespace App\Exports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AllFeaturesSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection(): Collection
    {
        $languages = Language::orderBy('id')->get();

        $features = FeaturesSetting::with('featuresSettingDetail')->orderBy('slug')->get();

        $rows = [];

        foreach ($features as $feature) {
            $detailsByLang = [];
            foreach ($feature->featuresSettingDetail as $detail) {
                $detailsByLang[$detail->language_id] = $detail;
            }

            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;

                $rows[] = [
                    'slug' => $feature->slug,
                    'features_setting_id' => $feature->id,
                    'language_id' => $lang->id,
                    'language' => $lang->name,
                    'name' => $detail?->name ?? '',
                    'tooltip' => $detail?->tooltip ?? '',
                    'icon' => $detail?->icon ?? '',
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Slug',
            'Features Setting ID',
            'Language ID',
            'Language',
            'Name',
            'Tooltip',
            'Icon',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}

