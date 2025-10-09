<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JogoController;

Route::get('/', function () {
    return view('welcome');
    
});

Route::resource('jogos', JogoController::class);
