<?php

namespace App\Admin\Actions\Student;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends RowAction
{
    public $name = 'Change Password';

    public function form() {
      $this->password('new_password',__('New password'))->placeholder("Enter new password for login of this student.")->rules('required');
    }
    public function handle(Model $model, Request $request) {
        $password = $request->get('new_password');
        $model->update([
          'password' => Hash::make($password)
        ]);
        return $this->response()->success('Password changed.')->refresh();
    }

}