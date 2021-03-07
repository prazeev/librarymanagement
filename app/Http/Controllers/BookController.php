<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller {
  public function __construct() {
    $this->middleware(['auth','verified']);
  }

  public function details($id) {
      $book = Book::find($id);
      $data = [
        'book' => $book,
        'cover' => Storage::url($book->book_cover)
      ];
      return view('book')->with($data);
    }
}
