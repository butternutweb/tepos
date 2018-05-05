<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Forgot extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email, string $forgot_token)
    {
        $this->link = route('auth.reset', ['email' => $email, 'forgot_token' => $forgot_token]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('TEPOS Forgot Password Service')->view('mails.forgot')->with([
            'link' => $this->link,
        ]);
    }
}
