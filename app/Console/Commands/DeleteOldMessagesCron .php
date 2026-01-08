<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Carbon\Carbon;

class DeleteOldMessagesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-old-messages:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete messages that have been marked as old for more than one month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oneMonthAgo = Carbon::now()->subMonth();

        Message::where('status', 'old')
            ->where('updated_at', '<', $oneMonthAgo)
            ->delete();
        
        return 0;
    }
}