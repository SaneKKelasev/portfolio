<?php

use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TechnologyController as AdminTechnologyController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectIndexController;
use App\Http\Controllers\ProjectShowController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::get('/projects', ProjectIndexController::class);
Route::get('/projects/{project:slug}', ProjectShowController::class);
Route::post('/contact', [ContactMessageController::class, 'store']);

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')
    ->prefix('admin')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('admin.dashboard');
        Route::get('/projects', [AdminProjectController::class, 'index']);
        Route::get('/projects/create', [AdminProjectController::class, 'create']);
        Route::post('/projects', [AdminProjectController::class, 'store']);
        Route::get('/projects/{project}/edit', [AdminProjectController::class, 'edit']);
        Route::put('/projects/{project}', [AdminProjectController::class, 'update']);
        Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy']);
        Route::get('/technologies', [AdminTechnologyController::class, 'index']);
        Route::post('/technologies', [AdminTechnologyController::class, 'store']);
        Route::put('/technologies/{technology}', [AdminTechnologyController::class, 'update']);
        Route::delete('/technologies/{technology}', [AdminTechnologyController::class, 'destroy']);
        Route::get('/contact-messages', [AdminContactMessageController::class, 'index']);
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show']);
        Route::patch('/contact-messages/{contactMessage}/read', [AdminContactMessageController::class, 'markAsRead']);
    });
