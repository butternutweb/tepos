<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Register extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $username, string $verification_token)
    {
        $this->link = route('auth.verify', ['username' => $username, 'verification_token' => $verification_token]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('TEPOS Registration Service')->view('mails.register')->with([
                'link' => $this->link,
            ]);
    }
}
