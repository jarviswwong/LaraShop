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

    public function setAttributesAttribute($json)
    {
        list($ids, $values) = array_divide($json);
        $attributes = collect([]);
        foreach ($ids as $key => $id) {
            $item = ['id' => $id, 'value' => $values[$key]];
            $attributes->push($item);
        }
        $this->attributes['attributes'] = $attributes->toJson();
    }

    public function getAttributesAttribute($json)
    {
        $attributes = json_decode($json, true);
        $format_array = collect([]);
        foreach ($attributes as $attribute) {
            $format_array->put($attribute['id'], $attribute['value']);
        }
        return $format_array->toJson();
    }
}
