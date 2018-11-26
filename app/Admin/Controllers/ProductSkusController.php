<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\ProductAttrValue;
use App\Models\ProductSku;
use App\Http\Controllers\Controller;
use App\Models\ProductSkuAttributes;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProductSkusController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index($product_id, Content $content)
    {
        return $content
            ->header('商品SKU列表')
            ->description('Product SKU List')
            ->body($this->grid($product_id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($product_id, $id, Content $content)
    {
        return $content
            ->header('编辑商品SKU')
            ->description('Edit Product SKU')
            ->body($this->form($product_id)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create($product_id, Content $content)
    {
        return $content
            ->header('新建商品SKU')
            ->description('Create Product SKU')
            ->body($this->form($product_id));
    }

    public function store($product_id)
    {
        $this->form($product_id)->store();
    }

    public function update($product_id, $id)
    {
        return $this->form($product_id)->update($id);
    }

    public function destroy($product_id, $id)
    {
        if ($this->form($product_id)->destroy($id)) {
            $data = [
                'status' => true,
                'message' => trans('admin.delete_succeeded'),
            ];

            // If destroy success, update the product price to the min one or zero.
            $product = Product::query()->where('id', $product_id)->first();
            $productSku = ProductSku::query()->where('product_id', $product_id)->get();
            if (!$productSku->isEmpty()) {
                $product->update(['price' => $productSku->min('price')]);
            } else {
                $product->update(['price' => '0.00']);
            }
        } else {
            $data = [
                'status' => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }

    /**
     * SKU展示Grid
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($product_id)
    {
        $grid = new Grid(new ProductSku);

        $grid->model()->where('product_id', $product_id);

        $grid->id('Id')->sortable();
        $grid->product_id('商品名称')
            ->display(function ($id) {
                $title = Product::where('id', $id)->pluck('title')->first();
                return $title;
            });
        $grid->title('SKU 名称');
        $grid->description('SKU 描述');
        $grid->price('价格');
        $grid->stock('库存');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * 编辑SKU表单
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($product_id)
    {
        $form = new Form(new ProductSku);

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        // Get All the attributes from this product
        $attributes = ProductSkuAttributes::query()
            ->where('product_id', $product_id)->get()
            ->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            })->all();

        $form->display('', '商品名称')
            ->with(function () use ($product_id) {
                $title = Product::where('id', $product_id)->pluck('title')->first();
                return $title;
            });

        $form->text('title', 'SKU 名称')->rules('required');
        $form->text('description', 'SKU 描述')->rules('required');
        $form->decimal('price', 'SKU 单价')->rules('required|min:0', [
            'min' => '价格不能设置为负数'
        ]);
        $form->number('stock', '库存')->min(0);

        $form->divider();

        // 如果为编辑状态，则填充SKU原有的属性数据
        $sku_id = request()->route()->parameter('id');
        $oldAttrValues = $form->model()->newQuery()
            ->where('id', $sku_id)->first();
        if ($oldAttrValues)
            $oldAttrValuesArray = $this->_getExistedFormData($oldAttrValues->getAttribute('attributes'), $product_id);
        else
            $oldAttrValuesArray = null;

        // 动态添加form
        foreach ($attributes as $id => $name) {
            $form->text('attr_' . $id, $name)
                ->default($oldAttrValuesArray ? $oldAttrValuesArray[$id] : '')
                ->rules('required', [
                    'required' => $name . '必须填写!',
                ]);
        }
        $attr_ids = array_keys($attributes);

        // 忽略不需要提交的字段
        $ignoreArr = array_map(function ($value) {
            return 'attr_' . ($value);
        }, $attr_ids);
        $form->ignore($ignoreArr);

        // form表单保存前执行
        $form->saving(function (Form $form) use ($product_id, $attr_ids, $ignoreArr) {
            // 更新product_attr_value表
            $attributes = '';
            foreach ($attr_ids as $key => $id) {
                $str = $this->_setAttrValue([
                    'product_id' => $product_id,
                    'value' => request('attr_' . $id),
                    'attr_id' => $id
                ]);
                $attributes .= $str . ';';
            }

            $form->model()->product_id = $product_id;
            $form->model()->attributes = rtrim($attributes, ';');
        });

        $form->saved(function () use ($product_id) {
            $product = Product::query()->where('id', $product_id)->first();
            $minPrice = ProductSku::query()->where('product_id', $product_id)->min('price');

            // 用最低的SKU价格来更新商品价格
            $product->price != $minPrice ? $product->update(['price' => $minPrice]) : '';
        });

        return $form;
    }

    // 获取已有的属性数据
    protected function _getExistedFormData($attrString, $product_id)
    {
        if ($attrString) {
            // 将每个symbol从字符串中取出来
            $attrString = explode(';', $attrString);
            $builder = ProductAttrValue::query()->where('product_id', $product_id);
            if ($attrString) {
                $result = $builder->whereIn('symbol', $attrString)->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['attr_id'] => $item['value']];
                    });
                return $result;
            }
        }
    }

    // 插入属性数据
    protected function _setAttrValue($array)
    {
        $query = ProductAttrValue::query()
            ->where('product_id', $array['product_id'])
            ->where('value', $array['value'])
            ->get();
        // 假如没有数据 则插入
        if ($query->isEmpty()) {
            $attr_symbol = ProductAttrValue::insertGetId($array);
            return $attr_symbol;
        }
        return $query->first()->symbol;
    }
}
