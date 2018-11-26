<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $casts = [
        'on_sale' => 'boolean'
    ];

    protected $appends = ['max_price'];

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

    public function attr_values()
    {
        return $this->hasMany(ProductAttrValue::class);
    }

    /**
     * This function turn ImageUrl into full links
     * When the blade template use '$product->image_url', this function will be called.
     * $this->image === $this->attributes['image']
     * @return mixed|string
     */
    public function getImageUrlAttribute()
    {
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return Storage::disk('public')->url($this->image);
    }

    // 获取所属SKU的最高价格
    public function getMaxPriceAttribute() {
        return $this->skus->max('price');
    }
}
