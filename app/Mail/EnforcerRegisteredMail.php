<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnforcerRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enforcer;

    /**
     * Create a new message instance.
     */
    public function __construct($enforcer)
    {
        $this->enforcer = $enforcer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Traffic Enforcer Registration Successful')
            ->view('enforcer.emails.enforcer_registered');
    }
}
