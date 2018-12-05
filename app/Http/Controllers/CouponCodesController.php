<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    public function verify(Request $request)
    {
        $code = $request->input('code');
        // 优惠券不存在
        if (!$couponCode = CouponCode::query()->where('code', $code)->first()) {
            abort(404);
        }

        // 优惠券未启用
        if (!$couponCode->enabled) {
            abort(404);
        }

        // 优惠券已被兑完
        if ($couponCode->total - $couponCode->used <= 0) {
            return response()->json(['msg' => '该优惠券已被兑完'], 403);
        }

        // 优惠券尚未开始使用
        if ($couponCode->not_before && $couponCode->not_before->gt(Carbon::now())) {
            return response()->json(['msg' => '该优惠券现在还无法使用'], 403);
        }

        // 优惠券已过期
        if ($couponCode->not_after && $couponCode->not_after->lt(Carbon::now())) {
            return response()->json(['msg' => '该优惠券已过期'], 403);
        }

        return $couponCode;
    }
}
