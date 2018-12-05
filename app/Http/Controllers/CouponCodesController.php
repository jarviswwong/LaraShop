<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    public function verify(Request $request)
    {
        $code = $request->input('code');

        if (!$couponCode = CouponCode::query()->where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        // 此处是校验优惠码，不需要传参
        $couponCode->checkCodeAvailable();

        return $couponCode;
    }
}
