<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller {
  public function index() {
    $transactions = Transaction::where('user_id','=', auth()->user()->id);
    $data = [
      'transactions' => $transactions->paginate()
    ];
    return view('transactions')->with($data);
  }

    public function borrow($id) {
      $book = Book::find($id);
      $book->transactions()->create([
        'resolved' => false,
        'user_id' => auth()->user()->id,
        'type' => 'borrow',
        'expected_closure' => now()->addDays(config('config.default_book_rent_days', 0))
      ]);
      Session::flash('status',__('Book Successfully borrowed.'));
      return back();
    }
    public function back($id) {
      $book = Book::find($id);
      $book->transactions()->create([
        'resolved' => false,
        'user_id' => auth()->user()->id,
        'type' => 'return',
        'expected_closure' => now()->addDays(config('config.default_book_rent_days', 0))
      ]);
      Session::flash('status',__('Book Successfully borrowed.'));
      return back();
    }
}
