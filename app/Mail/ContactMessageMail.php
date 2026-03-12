<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->contactData = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // On utilise le nom du client comme expéditeur pour l'affichage
        return $this->from(config('mail.from.address'), $this->contactData['name'])
                    ->replyTo($this->contactData['email'], $this->contactData['name'])
                    ->subject('Up Fiesta - Contact : ' . $this->contactData['subject'])
                    ->view('emails.contact-message');
    }
}