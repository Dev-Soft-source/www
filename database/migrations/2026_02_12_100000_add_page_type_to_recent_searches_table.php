<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Store which page the search was made from (e.g. pink_ride, folk_ride, search_ride).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recent_searches', function (Blueprint $table) {
            $table->string('page_type', 50)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recent_searches', function (Blueprint $table) {
            $table->dropColumn('page_type');
        });
    }
};
