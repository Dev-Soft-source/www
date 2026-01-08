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
            $table->text('features_option1_tooltip')->after('features_option1')->nullable();
            $table->text('features_option2_tooltip')->after('features_option2')->nullable();
            $table->text('features_option3_tooltip')->after('features_option3')->nullable();
            $table->text('features_option4_tooltip')->after('features_option4')->nullable();
            $table->text('features_option5_tooltip')->after('features_option5')->nullable();
            $table->text('features_option6_tooltip')->after('features_option6')->nullable();
            $table->text('features_option7_tooltip')->after('features_option7')->nullable();
            $table->text('features_option8_tooltip')->after('features_option8')->nullable();
            $table->text('features_option9_tooltip')->after('features_option9')->nullable();
            $table->text('features_option10_tooltip')->after('features_option10')->nullable();
            $table->text('features_option11_tooltip')->after('features_option11')->nullable();
            $table->text('features_option12_tooltip')->after('features_option12')->nullable();
            $table->text('features_option13_tooltip')->after('features_option13')->nullable();
            $table->text('features_option14_tooltip')->after('features_option14')->nullable();
            $table->text('features_option15_tooltip')->after('features_option15')->nullable();
            $table->text('features_option16_tooltip')->after('features_option16')->nullable();
        });

        DB::table('post_ride_page_setting_detail')->update([
            'features_option1_tooltip' => 'A ride specially designed for women, driven by women.',
            'features_option2_tooltip' => 'Enjoy a premium ride with added care and comfort.',
            'features_option3_tooltip' => 'Stay connected on the go with onboard Wi-Fi.',
            'features_option4_tooltip' => 'Infants are welcome, but passengers must bring their own baby seats.',
            'features_option5_tooltip' => 'Infants are welcome, and a car booster seat will be provided.',
            'features_option6_tooltip' => 'Children are welcome, but passengers must provide booster seats.',
            'features_option7_tooltip' => 'Children are welcome, and a car booster seat will be provided.',
            'features_option8_tooltip' => 'Travel in warmth with heating available in the vehicle.',
            'features_option9_tooltip' => 'Stay cool and comfortable with air conditioning.',
            'features_option10_tooltip' => 'Bring your bike along with a secure bike rack.',
            'features_option11_tooltip' => 'Travel with ease using a rack designed for skis.',
            'features_option12_tooltip' => 'Safety first: equipped with winter tires for snowy conditions.',
            'features_option13_tooltip' => 'Only passengers with a perfect 5-star review rating are allowed.',
            'features_option14_tooltip' => 'Only passengers with reviews of 4 stars or higher are accepted.',
            'features_option15_tooltip' => 'Only passengers with reviews of 3 stars or higher are accepted.',
            'features_option16_tooltip' => 'Only passengers with prior reviews can book this ride; no new users.',
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
            $table->dropColumn(['features_option16_tooltip', 'features_option15_tooltip', 'features_option14_tooltip', 'features_option13_tooltip',
                'features_option12_tooltip', 'features_option11_tooltip', 'features_option10_tooltip', 'features_option9_tooltip', 'features_option8_tooltip',
                'features_option7_tooltip', 'features_option6_tooltip', 'features_option5_tooltip', 'features_option4_tooltip', 'features_option3_tooltip',
                'features_option2_tooltip', 'features_option1_tooltip'
            ]);
        });
    }
};
