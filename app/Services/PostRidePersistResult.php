<?php

namespace App\Services;

use App\Models\Ride;
use App\Models\RideDetail;

/**
 * Outcome of {@see RidePostService::persist()} for shared web + API post/update ride flows.
 */
final class PostRidePersistResult
{
    private function __construct(
        public readonly bool $ok,
        public readonly ?string $errorMessage,
        public readonly ?string $errorHeading,
        public readonly bool $withFullRequestInput,
        public readonly ?string $uploadedImage,
        public readonly ?Ride $ride,
        public readonly ?RideDetail $rideDetail,
    ) {
    }

    public static function success(Ride $ride, RideDetail $rideDetail): self
    {
        return new self(true, null, null, false, null, $ride, $rideDetail);
    }

    public static function failure(
        string $errorMessage,
        ?string $errorHeading = null,
        bool $withFullRequestInput = false,
        ?string $uploadedImage = null,
    ): self {
        return new self(false, $errorMessage, $errorHeading, $withFullRequestInput, $uploadedImage, null, null);
    }
}
