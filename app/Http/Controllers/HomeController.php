<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
      $books = Book::where('id','>',0);
      if($request->has('search')) {
        $search = $request->search;
        $books = $books->where(function ($query) use ($search) {
          $query->orWhere('title','LIKE','%'.$search.'%');
          $query->orWhere('isbn','LIKE','%'.$search.'%');
          $query->orWhere('author','LIKE','%'.$search.'%');
        });
      }
      $data = [
        'books' => $books->paginate()
      ];
        return view('home')->with($data);
    }
}
