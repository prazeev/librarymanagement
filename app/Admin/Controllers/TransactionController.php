<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Transaction\ApproveTransaction;
use App\Admin\Actions\Transaction\RejectTransaction;
use App\Admin\Actions\Transaction\StarTransaction;
use App\Models\Book;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\Transaction;
use Symfony\Component\HttpFoundation\AcceptHeader;

class TransactionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Transaction';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Transaction());
        $grid->filter(function ($filter) {
          $filter->disableIdFilter();
          $filter->like('user.name',__("Student name"));
          $filter->where(function ($query) {
            $query->whereHas('books', function ($query) {
              $query->where('title', 'like', "%{$this->input}%")->orWhere('author', 'like', "%{$this->input}%");
            });
          }, __('Books'));
          $filter->scope('expiring', __('Expiring Transactions'))
            ->whereBetween('expected_closure', [now(), now()->addDays(7)])->where('resolved','=', false);
          $filter->scope('today_transaction', __('Today\'s Transactions'))
            ->whereBetween('created_at', [now()->subDays(1), now()]);
          $filter->scope('expired', __('Expired Transactions'))
            ->whereDate('expected_closure', '<', now())->where('resolved','=', false);
          $filter->between('created_at')->datetime();
        });
        $grid->actions(function ($actions) {
          $actions->disableView();
          $model = $actions->row;
          if($model->resolved) {
            $actions->add(new RejectTransaction);
          } else {
            $actions->add(new ApproveTransaction);
          }
        });
        $grid->column('id', __('Id'));
        $grid->column('resolved', __('Starred'))->action(StarTransaction::class)->filter([
          true => __('Starred'),
          false => __('Non starred')
        ]);
        $grid->column('user.name', __('Student'))->filter('like');
        $grid->column('type', __('Type'))->display(function ($type) {
          return ucwords($type);
        })->modal(__("Notes"), function ($model) {
          return $model->notes;
        });
        $grid->column('expected_closure', __('Expected closure'))->sortable()->editable();

        $grid->column('books', __('Books'))->display(function ($books) {
          $books = array_map(function ($book) {
            return "{$book['title']}";
          }, $books);
          return join(', ', $books);
        });
        $grid->column('updated_at', __('Updated at'))->diffForHumans();
        $grid->column('created_at', __('Created at'))->diffForHumans();

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
        $show = new Show(Transaction::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('type', __('Type'));
        $show->field('notes', __('Notes'));
        $show->field('expected_closure', __('Expected closure'));
        $show->field('resolved', __('Resolved'));
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
        $form = new Form(new Transaction());

        $form->select("user_id",__("Student"))->options(function ($id) {
          $user = User::find($id);
          if ($user) {
            return [$user->id => $user->name];
          }
        })->ajax('/'.config('admin.route.prefix').'/api/users')->required();
        $form->select('type', __('Type'))->options([
          'borrow' => __('Borrow'),
          'return' => __('Return'),
        ])->default('borrow')->required();
        $form->textarea('notes', __('Notes'))->placeholder(__('Transaction details...'));
        $form->datetime('expected_closure', __('Expected closure date'))->default(date('Y-m-d H:i:s'))->required();
        $form->listbox('books',__('Books'))->options(Book::all()->pluck('title','id'))->required();
        $form->saving(function (Form $form) {
          if($form->type == 'return') {
            return;
          }
          $out_of_stocks = [];
          $all_books = Book::all();
          foreach ($all_books as $book) {
            if($book->in_stock == 0) {
              $out_of_stocks[$book->id] = $book->title;
            }
          }
          $selected_books = array_values($form->books);
          foreach ($selected_books as $selected_book) {
            $selected_book = Book::find($selected_book);
            if($selected_book) {
              if(array_key_exists($selected_book->id, $out_of_stocks)) {
                throw new \Exception($selected_book->title.' is in out of stock.');
              }
            }
          }
        });
        return $form;
    }
}
