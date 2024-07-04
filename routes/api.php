<?php

use App\Http\Controllers\Apis\URLShortenerController;
use App\Http\Controllers\Apis\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

Route::prefix('/url-shortener')->name('url-shortener.')->controller(URLShortenerController::class)->group(function (){
    Route::any('/create', 'store')->name('create');
});
// require __DIR__.'/auth.php';
