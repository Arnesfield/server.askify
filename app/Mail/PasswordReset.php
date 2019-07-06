<?php

namespace App\Mail;

use App\User;

class PasswordReset extends BaseMail
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $code = null)
    {
        $code = $code ?: $user->reset_password;
        parent::__construct($user, $code);
    }

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
