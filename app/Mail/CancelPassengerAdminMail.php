<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelPassengerAdminMail extends Mailable
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
        $driver = $this->data['driver_name'] ?? 'Driver';
        $passenger = $this->data['passenger_name'] ?? 'Passenger';
        $date = isset($this->data['date']) ? \Carbon\Carbon::parse($this->data['date'])->format('M d, Y') : now()->format('M d, Y');
        return $this->markdown('mails/cancel_passenger_admin')
            ->subject("Passenger Removed: {$passenger} by {$driver} - {$date}")
            ->with("data", $this->data);
    }
}