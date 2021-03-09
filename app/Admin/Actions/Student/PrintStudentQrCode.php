<?php

namespace App\Admin\Actions\Student;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintStudentQrCode extends RowAction
{
    public $name = 'Print ID Card';
    public function dialog()
    {
      $this->confirm('Are you sure to print this QR code?');
    }
    public function handle(Model $model){
      $qr_code = QrCode::size(300)->generate(Crypt::encryptString($model->email));
      Storage::put('public/qrcodes/users/'.$model->id.'.svg', $qr_code);
      $url = Storage::url('public/qrcodes/users/'.$model->id.'.svg');
      return $this->response()->success('QR Code downloading....')->refresh()->download($url);
    }

}