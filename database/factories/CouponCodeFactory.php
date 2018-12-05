<?php

use Faker\Generator as Faker;

$factory->define(App\Models\CouponCode::class, function (Faker $faker) {
    $type = $faker->randomElement(array_keys(\App\Models\CouponCode::$typeMap));

    $value = $type === \App\Models\CouponCode::TYPE_FIXED ? random_int(1, 200) : random_int(1, 50);

    if ($type === \App\Models\CouponCode::TYPE_FIXED) {
        $min_amount = $value + 100;
    } else {
        // 百分比折扣，最低使用金额有50%为0
        if (random_int(0, 100) < 50) {
            $min_amount = 0;
        } else {
            $min_amount = random_int(100, 1000);
        }
    }

    return [
        'name' => join(' ', $faker->words),
        'description' => $faker->sentence,
        'code' => \App\Models\CouponCode::findAvailableCode(),
        'type' => $type,
        'value' => $value,
        'total' => 1000,
        'used' => 0,
        'min_amount' => $min_amount,
        'not_before' => null,
        'not_after' => null,
        'enabled' => true,
    ];
});
