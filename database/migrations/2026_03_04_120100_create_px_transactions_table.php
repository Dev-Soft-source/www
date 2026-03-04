<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('px_bookings')->cascadeOnDelete();
            $table->foreignId('ride_id')->constrained('px_rides')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('amount_minor');
            $table->char('currency', 3)->default('CAD');
            $table->string('provider', 32)->default('stripe');
            $table->string('type', 32)->default('charge');
            $table->string('status', 32)->default('succeeded');

            $table->string('stripe_payment_intent_id', 191)->nullable()->unique('px_txn_stripe_pi_uq');
            $table->string('stripe_payment_method_id', 191)->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamp('processed_at')->useCurrent();

            $table->timestamps();

            $table->index(['booking_id', 'status'], 'px_txn_booking_status_idx');
            $table->index(['ride_id', 'processed_at'], 'px_txn_ride_time_idx');
            $table->index(['user_id', 'processed_at'], 'px_txn_user_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_transactions');
    }
};

