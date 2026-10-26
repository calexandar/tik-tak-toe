<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/games')->name('dashboard');
    Route::resource('games', GameController::class)->only([ 'index','store', 'show', 'update']);
    Route::post('games/{game}/join', [GameController::class, 'join'])->name('games.join');
});

require __DIR__.'/settings.php';
