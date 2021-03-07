<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller {
  public function __construct() {
    $this->middleware(['auth','verified']);
  }
  public function scanCode($book_id) {
    $book = Book::find($book_id);
    $student = auth()->user();
    #1. Check if student have already borrowed
    $in_stock = $book->in_stock;
    $has_borrowed_previously = ($book->transactions()->where('user_id','=', $student->id)->where('type','=','borrow')->get()->count() - $book->transactions()->where('user_id','=', $student->id)->where('type','=','return')->get()->count()) > 0;
    if($has_borrowed_previously) {
      $book->transactions()->create([
        'resolved' => false,
        'user_id' => $student->id,
        'type' => 'return',
        'expected_closure' => now()
      ]);
      return "<H1>Successfully Returned ".$book->title."</H1>";
    } else if ($in_stock > 0) {
      $exp_time = now()->addDays(config('config.default_book_rent_days', 0));
      $book->transactions()->create([
        'resolved' => false,
        'user_id' => $student->id,
        'type' => 'borrow',
        'expected_closure' => $exp_time
      ]);
      return "<H1>Successfully Borrowed ".$book->title.'. You should return this book at '.$exp_time.'</H1>';
    } else {
      return "<H1>".$book->title." IS OUT OF STOCK</H1>";
    }
    return back();
  }

  public function index() {
    $transactions = Transaction::where('user_id','=', auth()->user()->id);
    $data = [
      'transactions' => $transactions->paginate()
    ];
    return view('transactions')->with($data);
  }

    public function borrow($id) {
      $book = Book::find($id);
      if($book->in_stock == 0) {
        Session::flash('status',__('Cannot borrow this item. Out of stock'));
        return back();
      }
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
