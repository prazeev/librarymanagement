<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Course;
use App\Models\MediaType;
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
        $this->middleware(['auth','verified']);
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
      if($request->has('mediatype') && $request->mediatype != '') {
        $mediatype = trim($request->mediatype);
        $books->wherehas('medias', function ($q) use ($mediatype) {
          $q->where('id','=', $mediatype);
        });
      }
      if($request->has('course') && $request->course != '') {
        $course = trim($request->course);
        $books->wherehas('courses', function ($q) use ($course) {
          $q->where('id','=', $course);
        });
      }
      $data = [
        'books' => $books->paginate(),
        'courses' => Course::get()->pluck('course','id'),
        'mediatype' => MediaType::get()->pluck('title','id'),
      ];
        return view('home')->with($data);
    }
}
