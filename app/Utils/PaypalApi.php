<?php

namespace App\Utils;

use PayPal\Api\Payment;
use Paypal\Rest\ApiContext;
use Paypal\Auth\OAuthTokenCredential;

class PaypalApi
{
    private static $context;

    public static function getContext()
    {
        if (!static::$context) {
            $config = config('paypal');
            static::$context = new ApiContext(
                new OAuthTokenCredential(
                    $config['client_id'],
                    $config['client_secret']
                )
            );
        }

        return static::$context;
    }

    public static function getToken()
    {
        // TODO: 
    }
}
