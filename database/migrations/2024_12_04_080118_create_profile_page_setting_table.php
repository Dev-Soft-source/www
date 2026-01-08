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
        Schema::create('profile_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('profile_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_page_setting_id')
                ->constrained()
                ->on('profile_page_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->string('main_heading')->nullable();
            $table->string('profile_setting_label')->nullable();
            $table->string('my_wallet_label')->nullable();
            $table->string('payment_options_label')->nullable();
            $table->string('payout_options_label')->nullable();
            $table->string('my_reviews_label')->nullable();
            $table->string('terms_condition_label')->nullable();
            $table->string('privacy_policy_label')->nullable();
            $table->string('terms_of_use_label')->nullable();
            $table->string('refund_policy_label')->nullable();
            $table->string('cancellation_policy_label')->nullable();
            $table->string('dispute_policy_label')->nullable();
            $table->text('contact_proximaride_label')->nullable();
            $table->string('logout_label')->nullable();
            $table->string('colse_your_contact_label')->nullable();
            $table->string('profile_page_setting')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_page_setting_detail');
        Schema::dropIfExists('profile_page_setting');
    }
};
