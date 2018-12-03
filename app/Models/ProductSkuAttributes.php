<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductSkuAttributes
 *
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductAttrValue[] $attr_values
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuAttributes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
