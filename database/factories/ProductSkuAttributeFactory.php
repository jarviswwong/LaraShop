<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ProductSkuAttribute::class, function (Faker $faker) {
    $name = $faker->randomElement([
        '属性一', '属性二', '属性三', '属性四', '属性五'
    ]);

    return [
        'name' => $name
    ];
});
