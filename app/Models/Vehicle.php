<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;

class Vehicle extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_id', 'make', 'model', 'type', 'license_no', 'color', 'year', 'car_type', 'image', 'original_image', 'remove_image', 'added_on', 'primary_vehicle'];
    protected $casts = [
        'type' => 'integer',
    ];

    protected const TYPE_ASSET_MAP = [
        38 => 'convertable.png',
        39 => 'Hatchback.png',
        40 => 'Coupe.png',
        41 => 'Minivan.png',
        42 => 'Sedan.png',
        43 => 'Station Wagon.png',
        44 => 'SUV.png',
        45 => 'Truck.png',
        46 => 'Van.png',
    ];

    protected const LEGACY_TYPE_NAME_MAP = [
        'convertable' => 38,
        'convertible' => 38,
        'hatchback' => 39,
        'coupe' => 40,
        'minivan' => 41,
        'sedan' => 42,
        'station wagon' => 43,
        'suv' => 44,
        'truck' => 45,
        'van' => 46,
    ];

    public static function normalizeVehicleTypeId($value): ?int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $normalized = strtolower(trim((string) $value));

        return self::LEGACY_TYPE_NAME_MAP[$normalized] ?? null;
    }

    protected function getDefaultVehicleImagePath(): string
    {
        $typeId = self::normalizeVehicleTypeId($this->attributes['type'] ?? null);
        $asset = self::TYPE_ASSET_MAP[$typeId] ?? 'car.png';

        return rtrim(config('app.url'), '/') . '/assets/' . $asset;
    }

    public function getTypeLabelAttribute(): ?string
    {
        $typeId = self::normalizeVehicleTypeId($this->attributes['type'] ?? null);

        if (!$typeId) {
            return null;
        }

        $selectedLanguage = Language::resolveLanguage(session('selectedLanguage'));
        $defaultLanguageId = Language::where('is_default', 1)->value('id') ?? 1;

        $detail = FeaturesSettingDetail::where('features_setting_id', $typeId)
            ->whereIn('language_id', array_filter([$selectedLanguage?->id, $defaultLanguageId]))
            ->get()
            ->sortByDesc(fn($item) => (int) ($selectedLanguage && $item->language_id == $selectedLanguage->id))
            ->first();

        return $detail?->name;
    }

    public function getImageAttribute($value)
    {
        if (isset($value) && $value != "") {
            return rtrim(config('app.url'), '/') . '/car_images/' . $value;
        }

        return $this->getDefaultVehicleImagePath();
    }

    public function getOriginalImageAttribute($value)
    {
        if (isset($value) && $value != "") {
            return rtrim(config('app.url'), '/') . '/car_images/' . $value;
        }

        return $this->getDefaultVehicleImagePath();
    }
}
