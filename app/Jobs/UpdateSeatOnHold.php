<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SeatDetail;
use Illuminate\Support\Facades\Log;

class UpdateSeatOnHold implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $seatId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($seatId)
    {
        $this->seatId = $seatId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $getSeatDetail = SeatDetail::where('id', $this->seatId)->where('status', 'hold')->first();
        if(isset($getSeatDetail) && !empty($getSeatDetail)){
            $getSeatDetail->status = 'pending';
            $getSeatDetail->user_id = NULL;
            $getSeatDetail->save();
        }
    }
}
