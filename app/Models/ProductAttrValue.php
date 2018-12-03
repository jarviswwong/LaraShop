<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductAttrValue
 *
 * @property int $symbol
 * @property int $product_id
 * @property string $value
 * @property int $attr_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSkuAttributes $skus_attribute
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue whereAttrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductAttrValue whereValue($value)
 * @mixin \Eloquent
 */
class ProductAttrValue extends Model
{
    protected $fillable = ['value'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 此处注意：必须指定外键名称，否则会自动认为关联的外键名为: product_sku_attributes_id
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function skus_attribute()
    {
        return $this->belongsTo(ProductSkuAttributes::class, 'attr_id', 'id');
    }
}
