<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
            'items' => 'required|array',
            'items.*.sku_id' => [  // 检查数组下的每一个sku_id
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        $fail('该商品不存在');
                        return;
                    }
                    if (!$sku->product->on_sale) {
                        $fail('该商品暂未开售');
                        return;
                    }
                    if ($sku->stock == 0) {
                        $fail('该商品已售完');
                    }
                    // 获取当前索引index
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];
                    $amount = $this->input('items')[$index]['amount'];
                    if ($amount > 0 && $amount > $sku->stock) {
                        $fail('该商品库存不足');
                        return;
                    }
                }
            ],
            'items.*.amount' => 'required|min:1|integer',
        ];
    }
}
