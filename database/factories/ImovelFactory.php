<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Imovel;
use Faker\Generator as Faker;

$factory->define(Imovel::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'state' => $faker->state,
        'city' => $faker->city,
        'address' => $faker->streetName,
        'number' => $faker->buildingNumber,
        'complement' => $faker->sentence($nbWords = 2, $variableNbWords = true) 
    ];
});
