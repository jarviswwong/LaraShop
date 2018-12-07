<?php

namespace App\Models;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InternalException;
use Carbon\Carbon;
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

    // 获取优惠券规则
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


    /**
     * 检查优惠券是否可用
     *
     * @param null $orderAmount
     * @throws \App\Exceptions\CouponCodeUnavailableException
     */
    public function checkCodeAvailable(User $user, $orderAmount = null)
    {
        if (!$this->enabled) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavailableException('该优惠券已被兑完');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券现在还无法使用');
        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券已过期');
        }

        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeUnavailableException('没有满足优惠券的最低使用金额');
        }

        // 检查用户是否能使用这张优惠券
        $is_coupon_used = Order::query()->where('user_id', $user->id)
            ->where('coupon_code_id', $this->id)
            // 这个代表一个括号
            ->where(function ($query) {
                // 未付款且未关闭的订单
                $query->where(function ($query) {
                    $query->whereNull('paid_at')
                        ->where('closed', false);
                })->orWhere(function ($query) {
                    // 已付款且未退款成功的订单
                    $query->whereNotNull('paid_at')
                        ->where('refund_status', '!=', Order::REFUND_STATUS_SUCCESS);
                });
            })->exists();
        if ($is_coupon_used) {
            throw new CouponCodeUnavailableException('你已经使用过这张优惠券了');
        }
    }

    // 获得使用优惠券后的价格
    public function getAdjustPrice($orderAmount)
    {
        if ($this->type === self::TYPE_FIXED) {
            return max(0.01, $orderAmount - $this->value);
        } else if ($this->type === self::TYPE_PERCENT) {
            return number_format(($orderAmount * (100 - $this->value)) / 100, '2', '.', '');
        } else {
            throw new InternalException('优惠券的类型异常');
        }
    }

    // 改变已使用优惠券数量
    public function changeUsed($increase = true)
    {
        if ($increase) {
            // 如果成功返回1，如果不是则返回0
            return $this->newQuery()
                ->where('id', $this->id)
                ->where('used', '<', $this->total)
                ->increment('used');
        } else {
            return $this->decrement('used');
        }
    }
}
