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
        Schema::table('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->text('required_field_label')->nullable();
            $table->text('designation_label')->nullable();
            $table->text('designation_option1')->nullable();
            $table->text('designation_option2')->nullable();
            $table->text('designation_option3')->nullable();
            $table->text('designation_option4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['designation_option4', 'designation_option3', 'designation_option2', 'designation_option1',
                'designation_label', 'required_field_label'
            ]);
        });
    }
};
