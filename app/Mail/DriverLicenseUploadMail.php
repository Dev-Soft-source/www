<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DriverLicenseUploadMail extends Mailable
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
        $name = trim(($this->data['first_name'] ?? '') . ' ' . ($this->data['last_name'] ?? '')) ?: 'Unknown';
        $date = $this->data['upload_date'] ?? now()->format('M d, Y');
        return $this->markdown('mails/driver_license_upload')
            ->subject("Driver License from {$name} - {$date} - Approval Required")
            ->with("data", $this->data);
    }
}