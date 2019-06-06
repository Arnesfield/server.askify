<?php

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'fname' => $faker->firstName,
        'mname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => 'secret',
        'email_verification_code' => '',
        'email_verified_at' => nowDt(),
    ];
});
