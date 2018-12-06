<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSkuAttribute;

class ProductAttrValuesSeeder extends Seeder
{
    public function run()
    {
        // 重置自增
        DB::statement('ALTER TABLE product_attr_values AUTO_INCREMENT=1');
        DB::statement('ALTER TABLE product_skus AUTO_INCREMENT=1');

        // 先填充productAttrValues
        Product::all()->each(function (Product $product) {
            ProductSkuAttribute::all()->where('product_id', $product->id)
                ->each(function (ProductSkuAttribute $attribute) use ($product) {
                    factory(\App\Models\ProductAttrValue::class, random_int(3, 6))->create([
                        'product_id' => $product->id,
                        'attr_id' => $attribute,
                    ]);
                });

            // 再填充productSku的attributes字段
            $symbols = collect($product->getProductSymbols('array'))
                ->map(function ($array) {
                    return collect($array)->random();
                })->toArray();
            factory(\App\Models\ProductSku::class, 10)->create([
                'product_id' => $product->id,
                'attributes' => implode(';', $symbols),
            ]);
            $product->update([
                'price' => $product->skus()->min('price'),
            ]);
        });
    }
}