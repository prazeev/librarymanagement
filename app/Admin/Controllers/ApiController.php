<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Department;
use App\Models\User;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class ApiController extends Controller {
  public function users(Request $request) {
    $q = $request->get('q');
    return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
  }
  public function books(Request $request) {
    $q = $request->get('q');
    return Book::where('title', 'like', "%$q%")->paginate(null, ['id', 'title as text']);
  }
  public function departments(Request $request) {
    $q = $request->get('q');
    return Department::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
  }
}