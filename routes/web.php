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
    $router->post('login', 'Auth\LoginController@index');
    $router->get('me', 'Auth\MeController@index');
    $router->post('register', 'Auth\RegisterController@index');
    $router->get('verify', 'Auth\VerifyEmailController@index');
    $router->post('resend', 'Auth\ResendVerificationCodeController@index');
});

// users
$router->group(['prefix' => 'users'], function() use ($router) {
    $router->get('', 'Core\UserController@index');
    $router->post('', 'Core\UserController@store');
    $router->get('{id}', 'Core\UserController@show');
    $router->patch('{id}', 'Core\UserController@update');
    $router->delete('{id}', 'Core\UserController@destroy');
    $router->patch('{id}/restore', 'Core\UserController@restore');
    
    // algo
    $router->get('{id}/question/feed', 'Core\UserController@questionFeed');
});

// questions
$router->group(['prefix' => 'questions'], function() use ($router) {
    $router->get('', 'Core\QuestionController@index');
    $router->post('', 'Core\QuestionController@store');
    $router->get('{id}', 'Core\QuestionController@show');
    $router->patch('{id}', 'Core\QuestionController@update');
    $router->delete('{id}', 'Core\QuestionController@destroy');
    $router->patch('{id}/restore', 'Core\QuestionController@restore');
});

// answers
$router->group(['prefix' => 'answers'], function() use ($router) {
    $router->get('', 'Core\AnswerController@index');
    $router->post('', 'Core\AnswerController@store');
    $router->get('{id}', 'Core\AnswerController@show');
    $router->patch('{id}', 'Core\AnswerController@update');
    $router->delete('{id}', 'Core\AnswerController@destroy');
    $router->patch('{id}/restore', 'Core\AnswerController@restore');
});

// tags
$router->group(['prefix' => 'tags'], function() use ($router) {
    $router->get('', 'Core\TagController@index');
    $router->post('', 'Core\TagController@store');
    $router->get('{id}', 'Core\TagController@show');
    $router->patch('{id}', 'Core\TagController@update');
    $router->delete('{id}', 'Core\TagController@destroy');
    $router->patch('{id}/restore', 'Core\TagController@restore');
});


//! non prod
if (env('APP_ENV') !== 'production') {
    $router->group(['prefix' => 'mail'], function() use ($router) {
        $router->get('verification', function () use ($router) {
            $user = \App\User::find(1);
            return new \App\Mail\EmailVerification($user);
        });
    });

    $router->get('mail/send', function () use ($router) {
        $user = \App\User::find(1);
        $res = $user->sendEmailVerificationCode();
        return jresponse($res);
        // return new \App\Mail\EmailVerification($user);
    });
}
