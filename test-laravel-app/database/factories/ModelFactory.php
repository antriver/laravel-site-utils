<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator;

$factory->define(
    \Antriver\LaravelSiteUtils\Users\User::class,
    function (Generator $faker) {
        return [
            'username' => $faker->userName,
            'email' => $faker->unique()->safeEmail,
            'password' => bcrypt('secret'),
        ];
    }
);
