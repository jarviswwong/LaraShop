<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reason' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'reason.required' => '退款原因必须填写',
        ];
    }
}
