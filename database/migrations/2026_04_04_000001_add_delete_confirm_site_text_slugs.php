<?php

use App\Models\Language;
use App\Models\SiteText;
use App\Models\SiteTextDetail;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Global confirm-dialog labels (SiteText / site_text_detail), keyed by slug for API and web.
     */
    public function up(): void
    {
        $defaultLang = Language::where('is_default', 1)->first();
        if (!$defaultLang) {
            return;
        }

        $definitions = [
            'btn_delete_it_text' => 'Yes, delete it!',
            'btn_take_me_back_text' => 'No, take me back!',
        ];

        foreach ($definitions as $slug => $text) {
            $siteText = SiteText::firstOrCreate(
                ['slug' => $slug],
                ['text' => $text]
            );

            SiteTextDetail::updateOrCreate(
                [
                    'slug_id' => $siteText->id,
                    'language_id' => $defaultLang->id,
                ],
                ['name' => $text]
            );
        }
    }

    public function down(): void
    {
        foreach (['btn_delete_it_text', 'btn_take_me_back_text'] as $slug) {
            $row = SiteText::where('slug', $slug)->first();
            if ($row) {
                $row->delete();
            }
        }
    }
};
