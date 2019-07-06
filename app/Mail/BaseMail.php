<?php

namespace App\Mail;

use App\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $code, $config, $date, $attrDate, $hiddenDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $code = null)
    {
        // get config
        config([
            'app.logo' => asset( env('APP_LOGO', 'img/logo.png') )
        ]);

        $config = config('app');
        $date = date('l, d F Y h:i A');
        $unix = strtotime(now());

        $this->user = $user;
        $this->code = $code;
        $this->config = $config;
        $this->date = $date;
        $this->attrDate = "data-datetime=$unix";
        $this->hiddenDate = "<span style=\"display: hidden\">$date</span>";
    }
}
