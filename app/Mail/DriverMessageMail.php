<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DriverMessageMail extends Mailable
{
    use Queueable, SerializesModels;
    
    private $data = [];
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->markdown('mails.driver_message')
            ->subject("New message received from ".$this->data['driver_first_name'])
            ->with("data", $this->data);
    }
}