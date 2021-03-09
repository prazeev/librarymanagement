<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DisplayController extends Controller {
  public function loginBarcode($code) {
    try {
      $email = Crypt::decryptString($code);
    } catch (\Exception $exception) {
      $email = $code;
//      return [
//        'error' => true,
//        'message' => $exception->getMessage()
//      ];
    }
    $user = \App\Models\User::where('email','=', $email)->first();
    if(!$user) {
      return [
        'error' => true,
        'message' => __('Not a valid user ID'),
      ];
    }
    try {
      Auth::loginUsingId($user->id);
      return [
        'error' => false,
        'message' => __('Logged in Successfully. Welcome '.$user->name.'!!'),
      ];
    } catch (\Exception $exception) {
      return [
        'error' => true,
        'message' => $exception->getMessage(),
      ];
    }

  }
    public function render() {
      return view('display');
    }
}
