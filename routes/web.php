<?php

$router->get('/key', function() {
    // generate app key, duh
    return str_random(32);
});

$router->get('/', function () use ($router) {
    // go back to root if in production
    if (env('APP_ENV') === 'production') {
        return redirect('../');
    }

    return $router->app->version();
});

// auth
$router->group(['prefix' => 'auth'], function() use ($router) {
    $router->get('login', 'Auth\LoginController@index');
    $router->get('me', 'Auth\MeController@index');
});

// users
$router->group(['prefix' => 'users'], function() use ($router) {
    $router->get('/', 'Core\UserController@index');
    $router->post('/', 'Core\UserController@store');
    $router->get('/{id}', 'Core\UserController@show');
    $router->patch('/{id}', 'Core\UserController@update');
    $router->delete('/{id}', 'Core\UserController@destroy');
    $router->patch('/{id}/restore', 'Core\UserController@restore');
});
