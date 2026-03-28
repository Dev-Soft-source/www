<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class NotificationMessageDetail extends Model
{
    use HasFactory;
    private const CACHE_VERSION_KEY = 'notification-text-detail:keyed-by-slug:version';

    protected $fillable = [
        'notification_message_id',
        'language_id',
        'message',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::bustKeyedBySlugCache());
        static::deleted(fn () => static::bustKeyedBySlugCache());
    }

    public function notificationMessage(): BelongsTo
    {
        return $this->belongsTo(NotificationMessage::class, 'slug_id');
    }



    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public static function getByLanguageKeyedBySlug(int $languageId, int $defaultLanguageId): array
    {
        $cacheKey = implode(':', [
            'notification-text-detail:keyed-by-slug',
            'v' . static::getCacheVersion(),
            'language-' . $languageId,
            'default-' . $defaultLanguageId,
        ]);

        return Cache::rememberForever($cacheKey, function () use ($languageId, $defaultLanguageId) {
            $messages = NotificationMessage::all()->keyBy('id');

            $details = static::whereIn('language_id', [$languageId, $defaultLanguageId])
                ->with('notificationMessage')
                ->get()
                ->groupBy('slug_id');

            $result = [];
            foreach ($messages as $message) {
                $slugDetails = $details->get($message->id);

                if ($slugDetails) {
                    $detail = $slugDetails->firstWhere('language_id', $languageId);
                    $defaultDetail = $slugDetails->firstWhere('language_id', $defaultLanguageId);

                    $name = null;
                    if ($detail && !empty($detail->message)) {
                        $name = $detail->message;
                    } elseif ($defaultDetail && !empty($defaultDetail->message)) {
                        $name = $defaultDetail->message;
                    }

                    $result[$message->slug] = $name;
                } else {
                    $result[$message->slug] = null;
                }
            }

            return $result;
        });
    }

    public static function bustKeyedBySlugCache(): void
    {
        Cache::forever(self::CACHE_VERSION_KEY, uniqid('', true));
    }

    protected static function getCacheVersion(): string
    {
        return (string) Cache::rememberForever(self::CACHE_VERSION_KEY, fn () => '1');
    }
}
