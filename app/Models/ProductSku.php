<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = [
        'title', 'description', 'price', 'stock', 'attributes'
    ];

    protected $casts = [
        'attributes' => 'json'
    ];

    public function product()
    {
        $this->belongsTo(Product::class);
    }
}
