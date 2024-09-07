<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\Rooms\CreateController;
use App\Http\Controllers\Rooms\Games\PlayController;
use App\Http\Controllers\Rooms\InviteController;
use App\Http\Controllers\Rooms\ShowController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', LoginController::class);

Route::post('/rooms', CreateController::class)
    ->name('rooms.create');

Route::get('/rooms/{uuid}', ShowController::class)
    ->name('rooms.show');

Route::post('/rooms/{uuid}/invite', InviteController::class)
    ->name('rooms.invite');

Route::post('/rooms/{uuid}/games', \App\Http\Controllers\Rooms\Games\CreateController::class)
    ->name('rooms.games.create');

Route::post('/rooms/{uuid}/games/{game}/play', PlayController::class)
    ->name('rooms.games.play');
