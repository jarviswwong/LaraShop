<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSkuAttributes extends Model
{
    protected $fillable = ['name'];

    public function product()
    {
        $this->belongsTo(Product::class);
    }
}
