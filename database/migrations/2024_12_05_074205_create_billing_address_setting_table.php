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
        Schema::create('billing_address_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('billing_address_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_add_setting_id')
                ->constrained()
                ->on('billing_address_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('main_heading')->nullable();
            $table->string('mobile_indicate_required_field_label')->nullable();
            $table->string('name_on_card_label')->nullable();
            $table->string('name_on_card_placeholder')->nullable();
            $table->string('card_number_label')->nullable();
            $table->string('card_number_placeholder')->nullable();
            $table->string('mobile_card_type_label')->nullable();
            $table->string('mobile_card_type_placholder')->nullable();
            $table->string('mobile_expiry_date_label')->nullable();
            $table->string('mobile_month_placeholder')->nullable();
            $table->string('mobile_year_placeholder')->nullable();
            $table->string('web_expiry_month_label')->nullable();
            $table->string('web_expiry_month_placeholder')->nullable();
            $table->string('security_code_label')->nullable();
            $table->string('security_code_palceholder')->nullable();
            $table->string('mobile_billing_address_label')->nullable();
            $table->string('mobile_street_name_label')->nullable();
            $table->string('mobile_street_name_placeholder')->nullable();
            $table->string('mobile_house_number_label')->nullable();
            $table->string('mobile_house_number_placeholder')->nullable();
            $table->string('mobile_city_label')->nullable();
            $table->string('mobile_city_placeholder')->nullable();
            $table->string('mobile_province_label')->nullable();
            $table->string('mobile_province_placeholder')->nullable();
            $table->string('mobile_country_label')->nullable();
            $table->string('mobile_country_placeholder')->nullable();
            $table->string('mobile_postal_code_label')->nullable();
            $table->string('mobile_postal_code_placeholder')->nullable();
            $table->string('mobile_primary_card_placeholder')->nullable();
            $table->string('save_button_text')->nullable();

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
        Schema::dropIfExists('billing_address_setting_detail');
        Schema::dropIfExists('billing_address_setting');
    }
};
