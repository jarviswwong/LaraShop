<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = factory(\App\Models\Product::class, 30)->create();
        foreach ($products as $product) {
            $product_attributes = factory(\App\Models\ProductSkuAttributes::class, 3)
                ->create(['product_id' => $product->id]);
            $skus = factory(\App\Models\ProductSku::class, 5)
                ->create(['product_id' => $product->id, 'attributes' => '{}']);
            $product->update(['price' => $skus->min('price')]);
        }
    }
}
