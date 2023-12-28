<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'getAllUsers']);
    Route::post('/register-user', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/add-book', [App\Http\Controllers\BookController::class, 'addBook']);
    Route::get('/books', [App\Http\Controllers\BookController::class, 'getAllBooks']);
    Route::post('/borrow-book', [App\Http\Controllers\BookController::class, 'borrowBook']);
    Route::get('/view-loans', [App\Http\Controllers\BookController::class, 'viewLoans']);
    Route::post('/approve-loan', [App\Http\Controllers\BookController::class, 'approveLoan']);
    Route::post('/decline-loan', [App\Http\Controllers\BookController::class, 'declineLoan']);
    Route::post('/return-book', [App\Http\Controllers\BookController::class, 'returnBook']);
    Route::post('/extend-loan', [App\Http\Controllers\BookController::class, 'extendLoan']);
    Route::post('/receive-book', [App\Http\Controllers\BookController::class, 'receiveBook']);
    // Add more protected routes here
});










