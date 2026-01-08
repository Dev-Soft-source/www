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
        Schema::create('contact_us_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('contact_us_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_page_setting_id')
                ->constrained()
                ->on('contact_us_page_setting')
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
            $table->text('meta_keywords', 1000)->nullable();
            $table->text('meta_description', 1000)->nullable();
            $table->string('main_heading')->nullable();
            $table->string('search_placeholder')->nullable();
            $table->string('search_button_label')->nullable();
            $table->string('choose_category_label')->nullable();
            $table->string('contact_us_heading')->nullable();
            $table->string('mailing_address_label')->nullable();
            $table->string('mailing_address_value')->nullable();
            $table->string('toll_free_label')->nullable();
            $table->string('toll_free_value')->nullable();
            $table->string('telephone_label')->nullable();
            $table->string('telephone_value')->nullable();
            $table->string('email_label')->nullable();
            $table->string('email_value')->nullable();
            $table->string('website_label')->nullable();
            $table->string('website_value')->nullable();
            $table->string('email_addresses_heading')->nullable();
            $table->string('inquires_label')->nullable();
            $table->string('inquires_value')->nullable();
            $table->string('sales_dept_label')->nullable();
            $table->string('sales_dept_value')->nullable();
            $table->string('ride_share_label')->nullable();
            $table->string('ride_share_value')->nullable();
            $table->string('office_hours_label')->nullable();
            $table->string('office_hours_value')->nullable();
            $table->string('get_touch_heading')->nullable();
            $table->string('name_email_placeholder')->nullable();
            $table->string('subject_placeholder')->nullable();
            $table->string('message_placeholder')->nullable();
            $table->string('submit_button_label')->nullable();
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
        Schema::dropIfExists('contact_us_page_setting_detail');
        Schema::dropIfExists('contact_us_page_setting');
    }
};
