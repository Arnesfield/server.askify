<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register any helper services.
     *
     * @return void
     */
    public function register()
    {
        $path = base_path('app/Helpers');
        $helpers = [
            'HumanizeDateHelper',
            'JsonResponseHelper',
            'RequestGetArrayHelper',
            'StatusHelper',
        ];

        foreach ($helpers as $helper) {
            require_once "$path/$helper.php";
        }
    }
}
