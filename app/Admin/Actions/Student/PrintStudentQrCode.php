<?php

namespace App\Admin\Actions\Student;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class PrintStudentQrCode extends RowAction
{
    public $name = 'Print ID Card';
    public function dialog()
    {
      $this->confirm('Are you sure to print this QR code?');
    }
    public function handle(Model $model){
      return $this->response()->success('ID card is downloading....')->refresh()->download(route('student.card',['id' => $model->id]));
    }

}