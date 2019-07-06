<?php

namespace App\Mail;

use App\User;

class PasswordReset extends BaseMail
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Reset Password')
            ->view('emails.passwordReset');
    }
}
