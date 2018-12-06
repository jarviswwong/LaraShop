<?php

use Illuminate\Database\Seeder;

class CouponCodesSeeder extends Seeder
{
    public function run()
    {
        // 重置自增
        DB::statement('ALTER TABLE coupon_codes AUTO_INCREMENT=1');

        factory(\App\Models\CouponCode::class, 20)->create();
    }
}
