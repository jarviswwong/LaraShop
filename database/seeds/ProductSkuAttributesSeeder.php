<?php

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSkuAttributesSeeder extends Seeder
{
    public function run()
    {
        // 重置自增
        DB::statement('ALTER TABLE product_sku_attributes AUTO_INCREMENT=1');

        Product::all()->each(function (Product $product) {
            factory(\App\Models\ProductSkuAttribute::class, random_int(2, 5))->create(['product_id' => $product->id]);
        });
    }
}
