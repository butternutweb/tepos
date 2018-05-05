<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $old_email, string $new_email, string $changeemail_token)
    {
        $this->link = route('profile.change', ['old_email' => $old_email, 'new_email' => $new_email, 'changeemail_token' => $changeemail_token]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('TEPOS Change Email Service')->view('mails.changeemail')->with([
            'link' => $this->link,
        ]);
    }
}
