<?php

use Illuminate\Support\Facades\Route;

// API-only backend - no web authentication routes
Route::get('/', function () {
    return view('welcome');
});
