<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClosedAccountMail extends Mailable
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
        $name = $this->data['name'] ?? 'Unknown';
        $date = $this->data['transaction_date'] ?? now()->format('M d, Y');
        return $this->markdown('mails/closed_account_mail')
            ->subject("Account Closed: {$name} - {$date}")
            ->with("data", $this->data);
    }
}