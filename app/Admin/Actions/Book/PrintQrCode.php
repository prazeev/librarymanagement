<?php

namespace App\Admin\Actions\Book;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintQrCode extends RowAction
{
    public $name = 'Download QR';
    public function dialog()
    {
      $this->confirm('Are you sure to print this QR code?');
    }
    public function handle(Model $model) {
      $qr_code = QrCode::size(300)->generate(Crypt::encryptString("book-".$model->id));
      Storage::put('public/qrcodes/'.$model->id.'.svg', $qr_code);
      $url = Storage::url('public/qrcodes/'.$model->id.'.svg');
      return $this->response()->success('QR Code downloading....')->refresh()->download($url);
    }

}