<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

abstract class CommonProductsController extends Controller
{
    use HasResourceActions;

    // 定义一个抽象方法，返回当前管理的商品类型
    abstract public function getProductType();

    public function index(Content $content)
    {
        return $content
            ->header(Product::$typeMap[$this->getProductType()] . '列表')
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑' . Product::$typeMap[$this->getProductType()])
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建' . Product::$typeMap[$this->getProductType()])
            ->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new Product);

        // 筛选出当前类型的商品，默认 ID 倒序排序
        $grid->model()->where('type', $this->getProductType())->orderBy('id', 'desc');
        // 调用自定义方法
        $this->customGrid($grid);

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();

            $actions->append('<a href="' . route('admin.product_skus.index', $actions->getKey()) . '"><i class="fa fa-shopping-cart"></i></a>
                              <a href="' . route('admin.productAttrValues.index', $actions->getKey()) . '"><i class="fa fa-tags"></i></a>');
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    // 定义一个抽象方法，各个类型的控制器将实现本方法来定义列表应该展示哪些字段
    abstract protected function customGrid(Grid $grid);

    protected function form()
    {
        $form = new Form(new Product);

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        // 隐藏字段type
        $form->hidden('type')->value($this->getProductType());
        $form->text('title', '商品名称')->rules('required');
        $form->image('image', '封面图片')
            ->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->rules('required|image');
        $form->editor('description', '商品描述')->rules('required');
        $form->radio('on_sale', '上架')
            ->options(['1' => '是', '0' => '否'])
            ->default('0');

        $this->customForm($form);

        $form->hasMany('skus_attributes', '商品分类属性',
            function (Form\NestedForm $attrForm) {
                $attrForm->text('name', '属性名')->rules('required');
            }
        );

        // Saving Function CallBack
        $form->saving(function (Form $form) {
            if (!$form->model()->price)
                $form->model()->price = '0.00';
        });

        return $form;
    }

    abstract protected function customForm(Form $form);
}