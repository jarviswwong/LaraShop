<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttrValue extends Model
{
    protected $fillable = ['value'];

    public function product()
    {
        $this->belongsTo(Product::class);
    }

    public function skus_attribute()
    {
        $this->belongsTo(ProductSkuAttributes::class);
    }
}
