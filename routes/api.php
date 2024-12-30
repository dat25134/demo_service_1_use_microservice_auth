<?php

use Illuminate\Support\Facades\Route;

Route::get('/posts', function () {
    return response()->json([
        'message' => 'Hello World',
    ]);
})->middleware('auth.jwt');
