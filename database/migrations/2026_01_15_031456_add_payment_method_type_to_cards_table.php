<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('payment_method_type')->default('card')->after('stripe_payment_method_id')->comment('card, paypal, apple_pay, google_pay');
            $table->string('paypal_email')->nullable()->after('payment_method_type');
            $table->string('paypal_payer_id')->nullable()->after('paypal_email');
            $table->text('payment_method_details')->nullable()->after('paypal_payer_id')->comment('JSON field for storing additional payment method details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn(['payment_method_type', 'paypal_email', 'paypal_payer_id', 'payment_method_details']);
        });
    }
};
