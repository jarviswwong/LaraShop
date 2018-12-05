<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Validation\Rule;

class CouponCodesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('优惠券列表')
            ->description('CouponCode List')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑优惠券')
            ->description('Edit CouponCode')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('新增优惠券')
            ->description('Add CouponCode')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode);

        //按照创建时间排序
        $grid->model()->orderBy('created_at', 'desc');
        $grid->id('ID')->sortable();
        $grid->name('名称');
        $grid->code('优惠码');
        $grid->coupon_rule('减免规则');
        $grid->total('优惠券总数');
        $grid->used('已用数目');
        $grid->column('usage', '用量')->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });
        $grid->enabled('是否启用')->display(function ($value) {
            return $value ? "是" : "否";
        });
        $grid->created_at('创建时间');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CouponCode::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->description('Description');
        $show->code('Code');
        $show->type('Type');
        $show->value('Value');
        $show->total('Total');
        $show->used('Used');
        $show->min_amount('Min amount');
        $show->not_before('Not before');
        $show->not_after('Not after');
        $show->enabled('Enabled');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode);

        $form->display('id', 'ID');
        $form->text('name', '名称')->rules('required');
        $form->text('description', '描述')->rules('required');
        // 不填写优惠码，就在saving中由系统自动生成
        $form->text('code', '优惠码')->rules(function ($form) {
            // 如果ID存在，则为编辑模式
            if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,' . $id . ',id';
            } else {
                return 'nullable|unique:coupon_codes,code';
            }
        });
        $form->radio('type', '类型')->options(CouponCode::$typeMap)->rules('required');
        // 通过类型type来校验value
        $form->text('value', '优惠数值')->rules(function ($form) {
            if ($form->type === CouponCode::TYPE_FIXED) {
                return 'required|numeric|min:0.01';
            } else {
                return 'required|numeric|between:1,99';
            }
        });
        $form->text('total', '总数')->rules('required|numeric|min:1');
        $form->text('min_amount', '最低金额')->rules('required|numeric|min:0');
        $form->datetime('not_before', '开始时间')->rules('date');
        $form->datetime('not_after', '结束时间')->rules('date|after:not_before', ['not_after.after' => '结束时间必须晚于开始时间']);
        $form->radio('enabled', '启用')->options(['1' => '是', '0' => '否']);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::findAvailableCode();
            }
        });
        return $form;
    }
}
