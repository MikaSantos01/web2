<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');


// =====================
//   BOOKS
// =====================

Route::middleware(['auth'])->group(function () {

    // Creates especiais
    Route::get('/books/create-id-number', [BookController::class, 'createWithId'])
        ->name('books.create.id')
        ->middleware('can:create,App\Models\Book');

    Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])
        ->name('books.store.id')
        ->middleware('can:create,App\Models\Book');

    Route::get('/books/create-select', [BookController::class, 'createWithSelect'])
        ->name('books.create.select')
        ->middleware('can:create,App\Models\Book');

    Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])
        ->name('books.store.select')
        ->middleware('can:create,App\Models\Book');

    // Resource (usa BookPolicy automaticamente)
    Route::resource('books', BookController::class);
});


// =====================
//   AUTHORS
// =====================

Route::middleware('auth')->group(function () {
    Route::resource('authors', AuthorController::class);
});


// =====================
//   PUBLISHERS
// =====================

Route::middleware('auth')->group(function () {
    Route::resource('publishers', PublisherController::class);
});


// =====================
//   CATEGORIES
// =====================

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class);
});


// =====================
//   USERS (ADMIN)
// =====================

Route::middleware(['auth'])->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('can:viewAny,App\Models\User');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware('can:view,user');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('can:update,user');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('can:update,user');
});


// =====================
//   BORROWINGS
// =====================

Route::middleware(['auth'])->group(function () {

    // Empréstimo (usa BookPolicy@borrow)
    Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])
        ->name('books.borrow')
        ->middleware('can:borrow,book');

    Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])
        ->name('users.borrowings');

    Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])
        ->name('borrowings.return')
        ->middleware('can:update,borrowing');
});
