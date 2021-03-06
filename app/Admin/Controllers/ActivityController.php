<?php

namespace App\Admin\Controllers;

use App\Models\Activity;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ActivityController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('活动');
            $content->description('列表');

            $content->body($this->grid());
        });
    }


    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('活动');
            $content->description('编辑');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('活动');
            $content->description('添加');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Activity::class, function (Grid $grid) {

            $grid->id('活动链接')->display(function ($id) {
                return 'http://' . $_SERVER['HTTP_HOST'] . '/wechat/activity/'. $id .'/openid/{openid}';
            });
            // ->sortable();
            $grid->column('title', '标题');
            $grid->start_time('起始时间')->display(function ($time) {
                return str_replace('00:00:00', '', $time);
            });
            $grid->end_time('终止时间')->display(function ($time) {
                return str_replace('00:00:00', '', $time);
            });
            $grid->get_score('通关分数')->sortable();
            $grid->activity_week('活动周期')->display(function ($weeks) {
                $weekArr = [1 => '周日', 2 => '周一', 3 => '周二', 4 => '周三', 5 => '周四', 6 => '周五', 7 => '周六'];
                $newWeeks = [];
                foreach ($weeks as $key => $val) {
                    $newWeeks[] = $weekArr[$val];
                }
                return implode(' , ', $newWeeks);
            });
            $grid->created_at('创建时间');
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableBatchDeletion();
            $grid->actions(function ($actions) {
                  $actions->disableDelete();
            });
            $this->gridSearch($grid);
        });
    }
    /**
     * 查询过滤
     * @param  [type] $grid [description]
     * @return [type]       [description]
     */
    public function gridSearch($grid)
    {
        $grid->filter( function ($filter) {

            $filter->useModal();
            // 禁用id查询框
            // $filter->disableIdFilter();
            $filter->like('title', '标题');
            $filter->between('start_time', '起始时间')->datetime();
        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Activity::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title', '列表标题')->unique();
            $form->dateRange('start_time', 'end_time', '有效时间');
            $form->number('get_score', '通关分数')->rules('required|min:0')->help('分数0 ~ 100');
            $weekArr = [1 => '星期日', 2 => '星期一', 3 => '星期二', 4 => '星期三', 5 => '星期四', 6 => '星期五', 7 => '星期六'];
            $form->multipleSelect('activity_week', '活动周期')->options($weekArr);
            $form->text('header', '分享标题');
            $form->text('des', '分享内容');
            $form->image('image', '分享图片');
            $form->textarea('rule', '列表规则')->rows(10);
            $form->select('status', '显示\隐藏')->options([0 => '隐藏', 1 => '显示'])->default(1);
        });
    }
}
