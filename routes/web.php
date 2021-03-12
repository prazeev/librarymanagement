<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/display', [App\Http\Controllers\DisplayController::class, 'render'])->name('front.display');
Route::get('/studentId/{id}', [App\Http\Controllers\DisplayController::class, 'studentCard'])->name('student.card');
Route::get('/loginBarcode/{code}', [App\Http\Controllers\DisplayController::class, 'loginBarcode'])->name('qr.login');
Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions');
Route::get('/books/{id}', [App\Http\Controllers\BookController::class, 'details'])->name('book.details');
Route::get('/transaction/qrcode', [App\Http\Controllers\QRScanner::class, 'scanner'])->name('qr.scanner');
Route::get('/transaction/book/{id}', [App\Http\Controllers\TransactionController::class, 'scanCode'])->name('book.transaction');
Route::get('/books/return/{id}', [App\Http\Controllers\TransactionController::class, 'back'])->name('book.return');
Route::get('/books/borrow/{id}', [App\Http\Controllers\TransactionController::class, 'borrow'])->name('book.borrow');
