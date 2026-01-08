<?php

namespace Database\Seeders;

use App\Models\NoShowHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNoShowHistoryRefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('no_show_history')->update(['is_refund' => 1]);
    }
}
