<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $no
 * @property int $user_id
 * @property array $address
 * @property float $total_amount
 * @property string|null $remark
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $payment_method
 * @property string|null $payment_no
 * @property string $refund_status
 * @property string|null $refund_no
 * @property bool $closed
 * @property bool $reviewed
 * @property string $ship_status
 * @property array|null $ship_data
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRefundNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereReviewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShipData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShipStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    // 订单状态
    const TYPE_NORMAL = 'normal';
    const TYPE_SECKILL = 'seckill';
    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品订单',
        self::TYPE_SECKILL => '秒杀商品订单',
    ];
    // 退款状态
    const REFUND_STATUS_PENDING = 'pending';  // 未申请退款
    const REFUND_STATUS_APPLIED = 'applied';    // 已申请退款
    const REFUND_STATUS_PROCESSING = 'processing';  // 退款中
    const REFUND_STATUS_SUCCESS = 'success';    // 退款成功
    const REFUND_STATUS_FAILED = 'failed';  // 退款失败
    // 物流状态
    const SHIP_STATUS_PENDING = 'pending';  // 未发货
    const SHIP_STATUS_DELIVERED = 'delivered';  // 已发货
    const SHIP_STATUS_RECEIVED = 'received';    // 已收货

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING => '未申请退款',
        self::REFUND_STATUS_APPLIED => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS => '退款成功',
        self::REFUND_STATUS_FAILED => '退款失败'
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED => '已收货'
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
        'type',
    ];

    protected $casts = [
        'closed' => 'boolean',
        'reviewed' => 'boolean',
        'address' => 'json',
        'ship_data' => 'json',
        'extra' => 'json'
    ];

    protected $dates = ['paid_at'];

    public static function findAvailableNo()
    {
        $prefix = date('YmdHis');

        for ($i = 0; $i < 10; $i++) {
            $no = $prefix . str_random(6);
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }

        \Log::warning('find order no failed!');
        return false;
    }

    // 监听模型创建事件，在写入数据库前触发
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::creating(function ($model) {
            if (!$model->no) {
                $model->no = static::findAvailableNo();
                // 生成失败
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    /**
     * 生成Uuid不重复的字符串
     *
     * @return string
     * @throws \Exception
     */
    public static function getAvailableRefundNo()
    {
        do {
            $refund_no = Uuid::uuid4()->getHex();
        } while (Order::query()->where('refund_no', $refund_no)->exists());

        return $refund_no;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 代表一个订单暂时只能用一个优惠码
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }
}
