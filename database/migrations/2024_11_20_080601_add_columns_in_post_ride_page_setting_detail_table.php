<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('smoking_option1_tooltip')->after('smoking_option1')->nullable();
            $table->text('smoking_option2_tooltip')->after('smoking_option2')->nullable();
            $table->text('animals_option1_tooltip')->after('animals_option1')->nullable();
            $table->text('animals_option2_tooltip')->after('animals_option2')->nullable();
            $table->text('animals_option3_tooltip')->after('animals_option3')->nullable();
        });

        DB::table('post_ride_page_setting_detail')->update([
            'smoking_option1_tooltip' => 'Smoking is not allowed.',
            'smoking_option2_tooltip' => 'No preference for smoking or non-smoking.',
            'animals_option1_tooltip' => "Animals are not allowed.",
            'animals_option2_tooltip' => 'Animals are allowed without restrictions.',
            'animals_option3_tooltip' => 'Only animals secured in cages are permitted.',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['animals_option3_tooltip', 'animals_option2_tooltip', 'animals_option1_tooltip',
                'smoking_option2_tooltip', 'smoking_option1_tooltip'
            ]);
        });
    }
};
