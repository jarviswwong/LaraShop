<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // 重置自增
        DB::statement('ALTER TABLE products AUTO_INCREMENT=1');

        factory(\App\Models\Product::class, 20)->create();
    }
}
