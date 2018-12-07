<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\ProductAttrValue;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductAttrValuesController extends Controller
{
    use HasResourceActions;

    public function index(Product $product, Content $content)
    {
        return $content
            ->header('调整商品属性')
            ->description('Adjust Product Attr Values')
            ->body(view('admin.product_attr_values.index', [
                'product' => $product->load(['skus_attributes', 'skus_attributes.attr_values']),
            ]));
    }

    public function changeOrder(Product $product, Request $request)
    {
        $attributes = collect(json_decode($request->input('_order'), true));
        foreach ($attributes as $index => $attribute) {
            foreach ($attribute['children'] as $key => $value) {
                $item = $product->attr_values()
                    ->where('attr_id', $attribute['id'])
                    ->find($value['symbol']);
                // 减少插入操作
                if ($item->order !== ($key + 1)) {
                    $item->order = ($key + 1);
                    $item->save();
                }
            }
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'product_id' => [
                'required',
                Rule::exists('products', 'id'),
            ],
            'value' => 'required',
            'attr_id' => ['required', Rule::exists('product_sku_attributes', 'id')],
            'order' => 'required|numeric',
        ], [], [
            'product_id' => '商品ID',
            'value' => '属性值',
            'attr_id' => '父属性ID',
            'order' => '排序',
        ]);

        $attr_value = new ProductAttrValue([
            'value' => $request->input('value'),
            'order' => $request->input('order'),
        ]);
        $attr_value->product()->associate($request->input('product_id'));
        $attr_value->skus_attribute()->associate($request->input('attr_id'));
        $attr_value->save();
    }
}
