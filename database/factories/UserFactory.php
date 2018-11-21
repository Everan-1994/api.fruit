<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    static $password;

    return [
        'name'           => $faker->name,
        'openid'         => str_random(15),
        'password'       => $password ?: $password = bcrypt('123456'),
        'created_at'     => now()->toDateTimeString(),
        'updated_at'     => now()->toDateTimeString(),
    ];
});
