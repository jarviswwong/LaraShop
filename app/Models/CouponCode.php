<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '百分比折扣',
    ];

    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled'
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    protected $dates = ['not_before', 'not_after'];

    protected $appends = ['coupon_rule'];

    /**
     * 生成有效的优惠码字符串
     *
     * @param int $length
     * @return string
     */
    public static function findAvailableCode($length = 16)
    {
        do {
            $code = strtoupper(str_random($length));
        } while (CouponCode::query()->where('code', $code)->exists());

        return $code;
    }

    public function getCouponRuleAttribute()
    {
        $min_amount = str_replace('.00', '', $this->attributes['min_amount']);
        $value = str_replace('.00', '', $this->attributes['value']);
        $min_amount_str = $this->attributes['min_amount'] > 0 ? "满 " . $min_amount . " " : "";

        switch ($this->attributes['type']) {
            case self::TYPE_FIXED:
                return $min_amount_str . "减免 " . $value;
                break;
            case self::TYPE_PERCENT:
                return $min_amount_str . "折扣 " . $value . "%";
                break;
        }
    }
}
