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

$factory->define(App\Question::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->realText(rand(32, 96)),
        'content' => $faker->paragraph(6),
    ];
});

$factory->define(App\Answer::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->paragraph(12),
    ];
});
