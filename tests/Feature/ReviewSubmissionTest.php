<?php

namespace Tests\Feature;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET review-passenger with non-existent booking UUID returns 404 view.
     */
    public function test_review_passenger_form_returns_404_view_for_invalid_booking_uuid(): void
    {
        $response = $this->get('/en/review-passenger/non-existent-uuid-12345');

        $response->assertViewIs('errors.404');
    }

    /**
     * GET review-driver with non-existent booking UUID returns 404 view.
     */
    public function test_review_driver_form_returns_404_view_for_invalid_booking_uuid(): void
    {
        $response = $this->get('/en/review-driver/non-existent-uuid-12345');

        $response->assertViewIs('errors.404');
    }

    /**
     * POST review-passenger/store with invalid booking id fails (no rating created).
     * When booking does not exist, controller may throw or redirect; we only assert no new rating.
     */
    public function test_review_passenger_store_with_invalid_booking_creates_no_rating(): void
    {
        $driver = User::factory()->create();
        $countBefore = Rating::count();

        $response = $this->actingAs($driver)->post('/review-passenger/store/99999', [
            '_token' => csrf_token(),
            'review' => 'Great passenger.',
            'conscious' => 5,
        ]);

        $this->assertDatabaseCount('ratings', $countBefore);
    }

    /**
     * POST review-driver/store with invalid booking id creates no rating.
     */
    public function test_review_driver_store_with_invalid_booking_creates_no_rating(): void
    {
        $passenger = User::factory()->create();
        $countBefore = Rating::count();

        $response = $this->actingAs($passenger)->post('/review-driver/store/99999', [
            '_token' => csrf_token(),
            'review' => 'Great driver.',
            'vehicle_condition' => 5,
        ]);

        $this->assertDatabaseCount('ratings', $countBefore);
    }

    /**
     * POST review-passenger/store with empty data returns validation errors (when booking exists).
     * Use this pattern once you have a valid booking (e.g. from seed or factory):
     *
     *   $booking = ...; // create driver, ride, passenger, booking
     *   $response = $this->actingAs($driver)->post(route('review_passenger.store', $booking->id), [
     *       '_token' => csrf_token(),
     *       'review' => '',
     *   ]);
     *   $response->assertSessionHasErrors(['review', 'conscious']);
     */
    public function test_review_passenger_validation_requires_review_and_at_least_one_rating(): void
    {
        // Skip if no way to create a valid booking in tests yet
        $this->markTestIncomplete(
            'Add a Booking + Ride + User setup (factory or seed) then POST with empty review and no stars; assert validation errors.'
        );
    }

    /**
     * POST review-driver/store with valid data creates type 1 rating (when booking exists).
     * Use this pattern once you have a valid booking:
     *
     *   $booking = ...;
     *   $response = $this->actingAs($passenger)->post(route('review_driver.store', $booking->id), [
     *       '_token' => csrf_token(),
     *       'review' => 'Smooth ride.',
     *       'vehicle_condition' => 5,
     *   ]);
     *   $response->assertRedirect();
     *   $this->assertDatabaseHas('ratings', ['type' => 1, 'posted_by' => $passenger->id]);
     */
    public function test_review_driver_store_creates_rating_when_valid(): void
    {
        $this->markTestIncomplete(
            'Add Booking + Ride + User setup then POST valid driver review; assert redirect and Rating type 1.'
        );
    }
}
