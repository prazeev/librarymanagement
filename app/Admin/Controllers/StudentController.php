<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Student\ChangePassword;
use App\Models\Department;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\User;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Hash;

class StudentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Student';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

      $grid->actions(function ($actions) {
        $actions->disableView();
        $actions->add(new ChangePassword);
      });
      $grid->filter(function ($filter) {
        $filter->disableIdFilter();
        $filter->like('name',__("Student name"));
      });

        $grid->column('id', __('ID'));
        $grid->column('name', __('Name'))->editable();
        $grid->column('email', __('Email'))->editable();
        $grid->column('departments', __('Departments'))->display(function ($departments) {
          $departments = array_map(function ($department) {
            return "<label class='label label-success'>{$department['name']}</label>";
          }, $departments);
          return join(' ', $departments);
        });
        $grid->column('gender',__('Gender'))->using([1 => 'Male', 2 => 'Female']);
        $grid->column('picture', __('Picture'))->image('',50,50);
        $grid->column('created_at', __('Created at'))->diffForHumans();
        $grid->column('updated_at', __('Updated at'))->diffForHumans()->sortable();

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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->text('name', __('Name'))->required();
        $form->email('email', __('Email'))->required();
        $form->multipleSelect('departments',__('Departments'))->options(Department::all()->pluck('name','id'))->required();
        $form->hidden('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        if($form->isCreating()) {
          $form->password('password', __('Password'))->required();
        }
        $form->image('picture', __('Picture'));
        $form->radio('gender',__('Gender'))->options([
          1 => __('Male'),
          2 => __('Female'),
        ])->default(1);
        $form->saving(function (Form $form) {
          if($form->isCreating()) {
            $form->password = Hash::make($form->password);
          }
        });


      return $form;
    }
}
