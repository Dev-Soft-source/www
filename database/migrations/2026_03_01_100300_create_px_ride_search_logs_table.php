<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_ride_search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('origin_city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('destination_city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('origin_label', 160)->nullable();
            $table->string('destination_label', 160)->nullable();
            $table->date('departure_date')->nullable();
            $table->unsignedTinyInteger('seats_required')->nullable();
            $table->unsignedInteger('results_count')->default(0);
            $table->json('filters')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('searched_at')->useCurrent();
            $table->timestamps();

            $table->index(['searched_at'], 'px_search_logs_searched_at_idx');
            $table->index(['user_id', 'searched_at'], 'px_search_logs_user_time_idx');
            $table->index(['origin_city_id', 'destination_city_id', 'departure_date'], 'px_search_logs_route_day_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_ride_search_logs');
    }
};

