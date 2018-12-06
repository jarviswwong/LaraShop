<?php

use Faker\Generator as Faker;
use App\Models\User;
use App\Models\Order;

$factory->define(App\Models\Order::class, function (Faker $faker) {
    $user = User::query()->inRandomOrder()->first();
    $address = $user->addresses()->inRandomOrder()->first();
    $refund = random_int(0, 10) < 1;
    $ship = $faker->randomElement(array_keys(Order::$shipStatusMap));
    $coupon = null;

    if (random_int(0, 10) < 3) {
        $coupon = \App\Models\CouponCode::query()->where('min_amount', 0)->inRandomOrder()->first();
        $coupon->changeUsed();
    }

    return [
        'address' => [
            'full_address' => $address->full_address,
            'zip' => $address->zip,
            'contact_name' => $address->contact_name,
            'contact_phone' => $address->contact_phone,
        ],
        'total_amount' => 0,
        'remark' => $faker->sentence,
        'paid_at' => $faker->dateTimeBetween('-30 days'), // 30天前到现在任意时间点
        'payment_method' => $faker->randomElement(['wechat', 'alipay']),
        'payment_no' => $faker->uuid,
        'refund_status' => $refund ? Order::REFUND_STATUS_SUCCESS : Order::REFUND_STATUS_PENDING,
        'refund_no' => $refund ? Order::getAvailableRefundNo() : null,
        'closed' => false,
        'reviewed' => random_int(0, 10) > 2,
        'ship_status' => $ship,
        'ship_data' => $ship === Order::SHIP_STATUS_PENDING ? null : [
            'express_company' => $faker->company,
            'express_no' => $faker->uuid,
        ],
        'extra' => $refund ? [
            'refund_count' => 1,
            'refund_index_1' => ['refund_reason' => $faker->sentence]
        ] : [],
        'user_id' => $user->id,
        'coupon_code_id' => $coupon ? $coupon->id : null,
    ];
});
