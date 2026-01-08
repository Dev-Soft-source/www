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
            $table->text('luggage_option1_tooltip')->after('luggage_option1')->nullable();
            $table->text('luggage_option2_tooltip')->after('luggage_option2')->nullable();
            $table->text('luggage_option3_tooltip')->after('luggage_option3')->nullable();
            $table->text('luggage_option4_tooltip')->after('luggage_option4')->nullable();
            $table->text('luggage_option5_tooltip')->after('luggage_option5')->nullable();
        });

        DB::table('post_ride_page_setting_detail')->update([
            'luggage_option1_tooltip' => 'Traveling light with no bags.',
            'luggage_option2_tooltip' => 'Perfect for essentials and compact items.',
            'luggage_option3_tooltip' => "Ideal for a few days' worth of clothing and accessories.",
            'luggage_option4_tooltip' => 'Great for extended trips or carrying bulkier items.',
            'luggage_option5_tooltip' => 'Extra space for oversized luggage or multiple bags.',
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
            $table->dropColumn(['luggage_option5_tooltip', 'luggage_option4_tooltip', 'luggage_option3_tooltip',
                'luggage_option2_tooltip', 'luggage_option1_tooltip'
            ]);
        });
    }
};
