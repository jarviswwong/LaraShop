<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ProductSkuAttributes::class, function (Faker $faker) {
    $name = $faker->randomElement([
        '套餐', '颜色', '内存', '发行地', '保修'
    ]);

    return [
        'name' => $name
    ];
});
