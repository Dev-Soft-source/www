<?php

namespace App\View\Composers;

use App\Models\ErrorPageSettingDetail;
use App\Models\Language;
use Illuminate\View\View;

class ErrorPageComposer
{
    /**
     * Get error page detail for the current (or default) language. Use when rendering 404 etc.
     */
    public static function getErrorPage(): ?ErrorPageSettingDetail
    {
        $locale = session('selectedLanguage') ?: app()->getLocale();
        $selectedLanguage = $locale ? Language::where('abbreviation', $locale)->first() : null;
        if (!$selectedLanguage) {
            $selectedLanguage = Language::where('is_default', 1)->first();
        }
        $defaultLang = Language::where('is_default', 1)->first();
        if ($selectedLanguage && $defaultLang) {
            return ErrorPageSettingDetail::getByLanguageWithFallback($selectedLanguage->id, $defaultLang->id);
        }
        return null;
    }

    /**
     * Bind error page settings (for 404 etc.) to the view.
     */
    public function compose(View $view): void
    {
        $view->with('errorPage', static::getErrorPage());
    }
}
