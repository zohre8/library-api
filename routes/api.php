<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;


Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me',[AuthController::class,'me']);
    Route::post('/auth/logout',[AuthController::class,'logout']);

    // Books
    Route::post('/books',[BookController::class,'store']);      // admin
    Route::put('/books/{book}',[BookController::class,'update']); // admin
    Route::delete('/books/{book}',[BookController::class,'destroy']); // admin

    // Borrow
    Route::post('/books/{book}/borrow',[BorrowController::class,'borrow']);
    Route::post('/borrows/{borrow}/return',[BorrowController::class,'returnBook']);
    Route::get('/me/borrows',[BorrowController::class,'myBorrows']);

    // Admin borrows
    Route::get('/borrows',[BorrowController::class,'all']); // admin
});

Route::get('/books',[BookController::class,'index']);
Route::get('/books/{book}',[BookController::class,'show']);
