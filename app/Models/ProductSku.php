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

    // Define extra attribute
    protected $appends = ['format_attributes'];

    /**
     * Set field format_attributes before save
     * @param $attributes
     */
    public function setFormatAttributesAttribute($attributes)
    {
        list($ids, $values) = array_divide($attributes);
        $attributes = collect([]);
        foreach ($ids as $key => $id) {
            $item = ['id' => $id, 'value' => $values[$key]];
            $attributes->push($item);
        }
        $this->attributes['attributes'] = $attributes->toJson();
    }

    /**
     * Modify field format_attributes before get
     * @return string
     */
    public function getFormatAttributesAttribute()
    {
        $attributes = json_decode($this->attributes['attributes'], true);
        $format_array = collect([]);
        foreach ($attributes as $attribute) {
            $format_array->put($attribute['id'], $attribute['value']);
        }
        return $format_array->toJson();
    }

    public function getAttributesAttribute() {
        return json_decode($this->attributes['attributes'], true);
    }

    public function product()
    {
        $this->belongsTo(Product::class);
    }
}
