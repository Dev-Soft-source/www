<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayoutSuccessMail extends Mailable
{
    use Queueable, SerializesModels;
    
    private $data = [];
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->markdown('mails/payout_success')
            ->subject("Your payout is on the way")
            ->with("data", $this->data);
    }
}