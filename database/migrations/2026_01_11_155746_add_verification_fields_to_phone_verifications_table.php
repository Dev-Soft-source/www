<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->string('channel')->default('sms')->after('verification_code')->comment('Channel used: sms or whatsapp');
            $table->string('twilio_verify_sid')->nullable()->after('channel')->comment('Twilio Verify Service SID for tracking');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->dropColumn(['channel', 'twilio_verify_sid']);
        });
    }
};
