<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification;
use Faker\Generator;

$factory->define(\Antriver\LaravelSiteScaffolding\Users\User::class, function (Generator $faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
    ];
});

$factory->define(\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification::class, function (Generator $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'token' => uniqid(),
        'type' => EmailVerification::TYPE_SIGNUP
    ];
});
