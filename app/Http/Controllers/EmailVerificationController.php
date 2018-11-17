<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmailVerificationController extends Controller
{
    /**
     * Verify email_verification URL
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function verify(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');
        $cache_key = 'email_verification_' . $email;

        // Verification URL is error
        if (!$email || !$token) {
            throw new InvalidRequestException('验证链接错误');
        }

        // Token is error or out-of-date
        if ($token != Cache::get($cache_key)) {
            throw new InvalidRequestException('验证链接不正确或者已过期');
        }

        // Can't find this user
        if (!$user = User::where('email', $email)->first()) {
            throw new InvalidRequestException('该用户不存在，验证邮箱失败');
        }

        // Remove Items from the cache
        Cache::forget($cache_key);

        $user->update(['email_verified' => true]);

        return view('pages.success', ['msg' => '恭喜您，验证邮箱成功']);
    }

    /**
     * Send Notification Email Manually
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if($user->email_verified) {
            throw new InvalidRequestException('您已验证邮箱，请不要重复验证');
        }

        // Send notification manually
        $user->notify(new EmailVerificationNotification());

        return view('pages.success', ['msg' => '邮件发送成功']);
    }
}
