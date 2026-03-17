<?php

namespace Tests\Unit;

use App\Models\Ride;
use PHPUnit\Framework\TestCase;

class RideFeatureDetectionTest extends TestCase
{
    public function test_string_features_detect_pink_and_extra_care_rides(): void
    {
        $ride = new Ride(['features' => '1=2=3']);

        $this->assertTrue($ride->isPinkRide());
        $this->assertTrue($ride->isExtraCareRide());
    }

    public function test_missing_pink_ride_feature_returns_false(): void
    {
        $ride = new Ride(['features' => '2=3']);

        $this->assertFalse($ride->isPinkRide());
        $this->assertTrue($ride->isExtraCareRide());
    }

    public function test_missing_extra_care_feature_returns_false(): void
    {
        $ride = new Ride(['features' => '1=3']);

        $this->assertTrue($ride->isPinkRide());
        $this->assertFalse($ride->isExtraCareRide());
    }

    public function test_empty_and_null_features_return_false(): void
    {
        $rideWithEmptyString = new Ride(['features' => '']);
        $rideWithNull = new Ride(['features' => null]);

        $this->assertFalse($rideWithEmptyString->isPinkRide());
        $this->assertFalse($rideWithEmptyString->isExtraCareRide());
        $this->assertFalse($rideWithNull->isPinkRide());
        $this->assertFalse($rideWithNull->isExtraCareRide());
    }

    public function test_array_input_supports_numeric_and_string_values(): void
    {
        $ride = new Ride();

        $this->assertTrue($ride->isPinkRide([1, 2, 3]));
        $this->assertTrue($ride->isExtraCareRide([1, 2, 3]));
        $this->assertTrue($ride->isPinkRide(['1', '2']));
        $this->assertTrue($ride->isExtraCareRide(['1', '2']));
        $this->assertTrue($ride->isPinkRide([1, ' 2 ', 3]));
        $this->assertTrue($ride->isExtraCareRide([1, ' 2 ', 3]));
    }

    public function test_feature_normalization_handles_whitespace_duplicates_and_malformed_strings(): void
    {
        $ride = new Ride(['features' => ' 1 == 2 =2= ']);

        $this->assertTrue($ride->isPinkRide());
        $this->assertTrue($ride->isExtraCareRide());
    }
}
