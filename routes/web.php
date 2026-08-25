<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectIndexController;
use App\Http\Controllers\ProjectShowController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::get('/projects', ProjectIndexController::class);
Route::get('/projects/{project:slug}', ProjectShowController::class);
Route::post('/contact', [ContactMessageController::class, 'store']);
