<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

$now = Carbon::now()->toDateTimeString();

$factory->define(App\User::class, function (Faker\Generator $faker) use ($now) {
    return [
        'fname' => $faker->firstName,
        'mname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => Hash::make('secret'),
        'email_verified_at' => $now,
    ];
});
