<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSkuAttributes extends Model
{
    protected $fillable = ['name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attr_values()
    {
        return $this->hasMany(ProductAttrValue::class, 'attr_id', 'id');
    }
}
