<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SearchController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/autocomplete', [SearchController::class, 'autocomplete']);
