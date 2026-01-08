<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BirthdayWishMail extends Mailable
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
        return $this->markdown('mails/birthday_wish')
            ->subject("Happy Birthday to our BEST member!")
            ->with("data", $this->data);
    }
}