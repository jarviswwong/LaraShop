<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{
    protected $fillable = [
        'title', 'description', 'price', 'stock', 'attributes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 加库存
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
}
