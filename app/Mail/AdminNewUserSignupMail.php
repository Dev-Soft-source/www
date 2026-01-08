<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AdminNewUserSignupMail extends Mailable implements ShouldQueue
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
        Log::info('AdminNewUserSignupMail sent', ['data' => $this->data]);
        return $this->markdown('mails/admin_new_user_signup')
            ->subject("New User Registration - " . $this->data['user_name'])
            ->with("data", $this->data);
    }
}

