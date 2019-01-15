<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('商品类目列表')
            ->description('Product Category List')
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品类目')
            ->description('Edit Product Category')
            ->body($this->form(true)->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建商品类目')
            ->description('Create Product Category')
            ->body($this->form(false));
    }

    protected function grid()
    {
        $grid = new Grid(new Category);

        $grid->id('ID')->sortable();
        $grid->name('名称');
        $grid->is_directory('是否有子目录')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->level('层级');
        $grid->path('类目路径');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }

    protected function form($isEditing = false)
    {
        $form = new Form(new Category);

        $form->text('name', '类目名称')->rules('required');

        if ($isEditing) {
            // 编辑模式
            $form->display('is_directory', '是否有子类目')->with(function ($value) {
                return $value ? '是' : '否';
            });

            $form->display('parent.name', '父类目');
        } else {
            $form->radio('is_directory', '是否有子类目')
                ->options(['0' => '否', '1' => '是'])
                ->default(0)
                ->rules(['required']);

            $form->select('parent_id', '父类目')->ajax('/admin/api/categories');
        }

        return $form;
    }

    // 通过ajax获取父类目的信息
    public function apiIndex(Request $request)
    {
        $search = $request->input('q');
        $result = Category::query()
            ->where('is_directory', true)// 此处选的是父类目
            ->where('name', 'like', '%' . $search . '%')
            ->paginate();

        $result->setCollection($result->getCollection()->map(function (Category $category) {
            return ['id' => $category->id, 'text' => $category->full_name];
        }));

        return $result;
    }
}
