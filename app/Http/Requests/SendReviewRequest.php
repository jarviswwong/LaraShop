<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reviews' => ['required', 'array'],
            'reviews.*.id' => [
                'required',
                Rule::exists('order_items', 'id')->where('order_id', $this->route('order')->id),
            ],
            'reviews.*.review' => ['required'],
            'reviews.*.rating' => ['required', 'between:1,5', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'reviews.*.review.required' => '评价必须填写',
            'reviews.*.rating.required' => '评分必须填写',
            'reviews.*.rating.between' => '评分必须介于1到5之间',
            'reviews.*.rating.integer' => '评分必须为整数',
        ];
    }
}
