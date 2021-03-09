<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\TransactionToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TransactionController extends Controller {
  public function __construct() {
    $this->middleware(['auth','verified']);
  }
  public function scanCode($book_id) {
    try {
      $details = Crypt::decryptString($book_id);
      $temp = explode("-", $details);
      if(count($temp) == 2 && $temp[0] == 'book') {
        $book_id = $temp[1];
      } else {
        throw new \Exception("Invalid book ID");
      }
    } catch (\Exception $exception) {
      return [
        'error' => true,
        'message' => $exception->getMessage(),
      ];
    }
    $book = Book::where('id','=', $book_id)->first();
    if(!$book) {
      return [
        'error' => true,
        'message' => 'Invalid Token',
      ];
    }
    $student = auth()->user();
    #1. Check if student have already borrowed
    $in_stock = $book->in_stock;
    $has_borrowed_previously = ($book->transactions()->where('user_id','=', $student->id)->where('type','=','borrow')->get()->count() - $book->transactions()->where('user_id','=', $student->id)->where('type','=','return')->get()->count()) > 0;
    if($has_borrowed_previously) {
      $last_borrowed = $book->transactions()->where('user_id','=', $student->id)->where('type','=','borrow')->first();
      if(!$last_borrowed) {
        $book->transactions()->create([
          'resolved' => false,
          'user_id' => $student->id,
          'type' => 'return',
          'expected_closure' => now()
        ]);
      } else {
        $last_transaction_on = Carbon::parse($last_borrowed->created_at);
        if($last_transaction_on->isAfter(now()->subSeconds(config('config.minimum_no_transaction_sec', 0)))) {
          return [
            'error' => true,
            'message' => 'You cannot make transaction so fast.',
          ];
        }
        $book->transactions()->create([
          'resolved' => false,
          'user_id' => $student->id,
          'type' => 'return',
          'expected_closure' => now()
        ]);
      }
      return [
        'error' => false,
        'message' => 'Successfully Returned '.$book->title,
      ];
    } else if ($in_stock > 0) {
      $exp_time = now()->addDays(config('config.default_book_rent_days', 0));
      $book->transactions()->create([
        'resolved' => false,
        'user_id' => $student->id,
        'type' => 'borrow',
        'expected_closure' => $exp_time
      ]);
      return [
        'error' => false,
        'message' => "Successfully Borrowed ".$book->title.'. You should return this book at '.$exp_time,
      ];
    } else {
      return [
        'error' => false,
        'message' => "".$book->title.' IS OUT OF STOCK',
      ];
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
