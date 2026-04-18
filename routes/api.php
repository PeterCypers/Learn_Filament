<?php

// use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);

// Route::get('/categories', function () {
//     return Category::all();
// })->name('api.categories');
