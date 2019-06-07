<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    protected $helpersPath = 'app/Helpers';

    protected $helpers = [
        'AuthUserHelper',
        'CarbonHelper',
        'ErrorHelper',
        'HumanizeDateHelper',
        'JsonResponseHelper',
        'RequestGetArrayHelper',
        'StatusHelper',
    ];
    /**
     * Register any helper services.
     *
     * @return void
     */
    public function register()
    {
        $path = base_path($this->helpersPath);
        foreach ($this->helpers as $helper) {
            require_once "$path/$helper.php";
        }
    }
}
