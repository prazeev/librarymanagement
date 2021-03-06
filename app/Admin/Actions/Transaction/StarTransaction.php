<?php

namespace App\Admin\Actions\Transaction;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class StarTransaction extends RowAction
{
    public $name = 'Star';

    public function handle(Model $model) {
        // Switch the value of the `star` field and save
        $model->resolved = (int) !$model->resolved;
        $model->save();

        // return a new html to the front end after saving
        $html = !$model->resolved ? "<i class=\"fa fa-star-o\"></i>" : "<i class=\"fa fa-star\"></i>" ;

        return $this->response()->html($html);
    }
    public function display($resolved) {
      return !$resolved ? "<i class=\"fa fa-star-o\"></i>" : "<i class=\"fa fa-star\"></i>";
    }

}