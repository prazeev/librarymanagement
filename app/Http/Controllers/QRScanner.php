<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QRScanner extends Controller {
  public function __construct() {
    $this->middleware(['auth','verified']);
  }
    public function scanner() {
      return view('qrscanner');
    }
}
