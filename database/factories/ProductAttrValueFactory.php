<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ProductAttrValue::class, function (Faker $faker) {
    return [
        'value' => $faker->unique()->words(3, true),
    ];
});
