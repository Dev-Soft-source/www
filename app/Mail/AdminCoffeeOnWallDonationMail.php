<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminCoffeeOnWallDonationMail extends Mailable
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
        $name = $this->data['donor_name'] ?? 'Donor';
        $date = $this->data['transaction_date'] ?? now()->format('F j, Y');
        return $this->markdown('mails/admin_coffee_on_wall_donation')
            ->subject("Coffee on the Wall: {$name} - {$date} - \$" . number_format($this->data['amount'], 2))
            ->with("data", $this->data);
    }
}

