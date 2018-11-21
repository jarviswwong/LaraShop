<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $casts = [
        'on_sale' => 'boolean'
    ];

    // One product has many skus
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    // One product has many skusAttributes
    public function skus_attributes()
    {
        return $this->hasMany(ProductSkuAttributes::class);
    }
}
