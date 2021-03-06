<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ApproveTransaction extends RowAction
{
    public $name = 'Approve';

    public function handle(Model $model) {
      $model->update([
        'resolved' => true
      ]);
        return $this->response()->success('Transaction Approved.')->refresh();
    }

}