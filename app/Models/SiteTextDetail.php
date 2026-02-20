<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteTextDetail extends Model
{
    use HasFactory;

    public $table = "site_text_detail";

    protected $fillable = [
        'slug_id',
        'language_id',
        'name',
        'icon',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function siteText(): BelongsTo
    {
        return $this->belongsTo(SiteText::class, 'slug_id');
    }

    /**
     * Get all SiteTextDetail records for a specific language, organized by SiteText slug
     * Returns an array like [slug => detail->name]
     * Includes all SiteText records, even if no SiteTextDetail exists for the language
     * Falls back to default language (language_id = 1) if name is null or empty
     *
     * @param int $languageId
     * @return array
     */
    public static function getByLanguageKeyedBySlug(int $languageId, int $defaultLanguageId): array
    {
        // Get all SiteText records
        $siteTexts = SiteText::all()->keyBy('id');
        
        // Get all SiteTextDetail records for the specified language and default language (1)
        $details = static::whereIn('language_id', [$languageId, 1])
            ->with('siteText')
            ->get()
            ->groupBy('slug_id');
        
        // Build the result array: [slug => name]
        $result = [];
        foreach ($siteTexts as $siteText) {
            $slugDetails = $details->get($siteText->id);
            
            if ($slugDetails) {
                // Get detail for requested language
                $detail = $slugDetails->firstWhere('language_id', $languageId);
                // Get default language detail (language_id = 1)
                $defaultDetail = $slugDetails->firstWhere('language_id', $defaultLanguageId);
                
                // Use requested language name if not null/empty, otherwise fallback to default
                $name = null;
                if ($detail && !empty($detail->name)) {
                    $name = $detail->name;
                } elseif ($defaultDetail && !empty($defaultDetail->name)) {
                    $name = $defaultDetail->name;
                }
                
                $result[$siteText->slug] = $name;
            } else {
                $result[$siteText->slug] = null;
            }
        }
        
        return $result;
    }
}
