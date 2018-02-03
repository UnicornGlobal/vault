<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'app_id' => 'B3D74DE8-A8AA-495E-A652-D779CC6F044C',
        'app_key' => '1779234-44D9-4AE1-A864-3DDB1739B970',
    ];
});
