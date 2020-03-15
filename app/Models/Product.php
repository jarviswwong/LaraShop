<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property bool $on_sale
 * @property float $rating
 * @property int $sold_count
 * @property int $review_count
 * @property float $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductAttrValue[] $attr_values
 * @property-read mixed|string $image_url
 * @property-read mixed $max_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductSku[] $skus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductSkuAttribute[] $skus_attributes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereOnSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereReviewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSoldCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    const TYPE_NORMAL = 'normal';
    const TYPE_SECKILL = 'seckill';
    public static $typeMap = [
        self::TYPE_NORMAL  => '普通商品',
        self::TYPE_SECKILL => '秒杀商品',
    ];

    protected $fillable = [
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price', 'type'
    ];

    protected $casts = [
        'on_sale' => 'boolean'
    ];

    protected $appends = ['max_price'];

    // 获取该商品下由attr_value的symbol字段组成的数组
    public function getProductSymbols($returnType)
    {
        $result = $this->attr_values()->orderBy('order')->get()
            ->sortBy('attr_id')
            ->groupBy('attr_id')
            ->map(function ($item) {
                return $item->pluck('symbol');
            })->values();

        switch ($returnType) {
            case "json":
                return $result->toJson();
                break;
            case "array":
                return $result->toArray();
                break;
            default:
                return $result;
                break;
        }
    }

    /**
     * 获取商品下SKU并格式化:
     * "[symbols;] => [price, stock]"
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductSkuItems()
    {
        return $this->skus
            ->mapWithKeys(function ($item) {
                return [
                    $item['attributes'] => [
                        'sku_id' => $item['id'],
                        'price' => $item['price'],
                        'stock' => $item['stock']
                    ]
                ];
            });
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function skus_attributes()
    {
        return $this->hasMany(ProductSkuAttribute::class);
    }

    public function attr_values()
    {
        return $this->hasMany(ProductAttrValue::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seckill()
    {
        return $this->hasOne(SeckillProduct::class);
    }

    // 获取图片的完整URL
    public function getImageUrlAttribute()
    {
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return Storage::disk('public')->url($this->image);
    }

    // 获取所属SKU的最高价格
    public function getMaxPriceAttribute()
    {
        return $this->skus->max('price');
    }
}
