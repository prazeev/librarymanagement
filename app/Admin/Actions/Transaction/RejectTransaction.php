<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class RejectTransaction extends RowAction
{
    public $name = 'Reject';

    public function handle(Model $model)
    {
      $model->update([
        'resolved' => false
      ]);
      return $this->response()->success('Transaction Rejected.')->refresh();
    }

}