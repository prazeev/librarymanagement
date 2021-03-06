<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    public function index(Content $content) {
        return $content
            ->title('Dashboard')
            ->row(function (Row $row) {
                $row->column(3, function (Column $column) {
                  $infoBox = new InfoBox(__('Total Books'), 'book', 'aqua', url(config('admin.route.prefix').'/books'), Book::all()->count());
                  $column->append($infoBox->render());
                });

                $row->column(3, function (Column $column) {
                  $infoBox = new InfoBox(__('Total Students'), 'graduation-cap', 'green', url(config('admin.route.prefix').'/students'), User::all()->count());
                  $column->append($infoBox->render());
                });

                $row->column(3, function (Column $column) {
                  $transactions = Transaction::whereBetween('created_at', [now()->subDays(1), now()])->count();
                  $infoBox = new InfoBox(__("Today's Transactions"), 'suitcase', 'yellow', url(config('admin.route.prefix').'/transactions?&_scope_=today_transaction'), $transactions);
                  $column->append($infoBox->render());
                });

              $row->column(3, function (Column $column) {
                $transactions = Transaction::whereDate('expected_closure', '<=', now())->where('resolved','=', false)->count();
                $infoBox = new InfoBox(__("Expired Transactions"), 'sign-out', 'red', url(config('admin.route.prefix').'/transactions?&_scope_=expired'), $transactions);
                $column->append($infoBox->render());
              });
            });
    }
}
