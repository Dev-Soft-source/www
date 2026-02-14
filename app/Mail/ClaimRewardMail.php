<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClaimRewardMail extends Mailable
{
    use Queueable, SerializesModels;
    private $data = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->data['person_name'] ?? 'A user';
        $date = $this->data['transaction_date'] ?? now()->format('F j, Y');
        return $this->markdown('mails/claim_reward')
            ->subject("Reward Claim from {$name} - {$date}")
            ->with("data", $this->data);
    }
}