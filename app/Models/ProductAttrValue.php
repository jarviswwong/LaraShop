<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
