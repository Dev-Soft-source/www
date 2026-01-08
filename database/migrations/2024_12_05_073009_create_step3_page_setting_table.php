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
        Schema::create('step3_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('step3_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step3_page_setting_id')
                ->constrained()
                ->on('step3_page_setting')
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
            $table->string('main_label')->nullable();
            $table->string('required_label')->nullable();
            $table->string('make_label')->nullable();
            $table->string('model_label')->nullable();
            $table->string('vehicle_type_label')->nullable();
            $table->string('color_label')->nullable();
            $table->string('license_label')->nullable();
            $table->string('year_label')->nullable();
            $table->string('fuel_label')->nullable();
            $table->string('electric_option_label')->nullable();
            $table->string('hybrid_option_label')->nullable();
            $table->string('gas_option_label')->nullable();
            $table->string('driver_license_label')->nullable();
            $table->string('driver_license_sub_label')->nullable();
            $table->string('photo_label')->nullable();
            $table->string('skip_button_label')->nullable();
            $table->string('next_button_label')->nullable();
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
        Schema::dropIfExists('step3_page_setting_detail');
        Schema::dropIfExists('step3_page_setting');
    }
};
