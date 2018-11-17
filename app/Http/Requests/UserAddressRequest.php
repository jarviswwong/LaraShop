<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'address' => 'required|max:70',
            'zip' => 'required',
            'contact_name' => 'required',
            'contact_phone' => 'required|regex:/^1[34578]\d{9}$/',
        ];
    }

    public function attributes()
    {
        return [
            'province' => '省',
            'city' => '城市',
            'district' => '地区',
            'address' => '详细地址',
            'zip' => '邮编',
            'contact_name' => '姓名',
            'contact_phone' => '手机号',
        ];
    }

    public function messages()
    {
        return [
            'address.max' => "地址长度不能超过70个字",
            'contact_phone.regex' => '手机号格式不正确，请检查'
        ];
    }
}
