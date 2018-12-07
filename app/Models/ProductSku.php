<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

/**
 * App\Models\ProductSku
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property float $price
 * @property int $stock
 * @property string $attributes
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $attr_array
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductSku extends Model
{
    protected $fillable = [
        'title', 'description', 'price', 'stock', 'attributes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getAttrArrayAttribute()
    {
        return explode(';', $this->attributes['attributes']);
    }

    /**
     * 加库存
     *
     * @param $amount
     * @throws InternalException
     */
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存的数量不可小于0');
        }

        $this->increment('stock', $amount);
    }

    /**
     * 减库存操作，防止库存减到负数，引起超售
     *
     * @param $amount
     * @return int
     * @throws InternalException
     */
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存的数量不可小于0');
        }

        return $this->newQuery()
            ->where('id', $this->id)
            ->where('stock', '>=', $amount)
            ->decrement('stock', $amount);
    }

    // 判断sku是否存在某属性值
    public function hasAttrValue($symbol)
    {
        $attributes = $this->attributes['attributes'];
        if ($attributes) {
            $array = explode(';', $attributes);
            if (in_array($symbol, $array)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
