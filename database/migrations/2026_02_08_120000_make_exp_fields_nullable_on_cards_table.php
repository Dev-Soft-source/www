<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exp_month/exp_year are only relevant for card payment methods; PayPal and others don't have expiry.
     */
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('exp_month')->nullable()->change();
            $table->string('exp_year')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('exp_month')->nullable(false)->change();
            $table->string('exp_year')->nullable(false)->change();
        });
    }
};
