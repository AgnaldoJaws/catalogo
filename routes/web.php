<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Admin\{
    DashboardController, ProfileController, LocationController,
    MenuSectionController, MenuItemController
};

// Público
Route::get('/', [HomeController::class, 'index'])->name('web.home');
Route::get('/empresa/{slug}', [BusinessController::class, 'show'])->name('web.business.show');

// Auth (Breeze/Fortify/etc.)
require __DIR__.'/auth.php';

// Admin (empresa)
Route::middleware(['web','auth','verified','role:owner,empresa,superadmin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        Route::middleware('business.access')->group(function () {

            Route::get('business/{business}', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('business/{business}/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');
            Route::post('business/{business}/profile', [ProfileController::class, 'update'])
                ->name('profile.update');

            Route::get('business/{business}/locations', [LocationController::class, 'index'])
                ->name('locations.index');
            Route::post('business/{business}/locations', [LocationController::class, 'store'])
                ->name('locations.store');
            Route::delete('business/{business}/locations/{location}', [LocationController::class, 'destroy'])
                ->name('locations.destroy');

            Route::get('business/{business}/menu/sections', [MenuSectionController::class, 'index'])
                ->name('menu.sections.index');
            Route::post('business/{business}/menu/sections', [MenuSectionController::class, 'store'])
                ->name('menu.sections.store');
            Route::post('business/{business}/menu/sections/{section}', [MenuSectionController::class, 'update'])
                ->name('menu.sections.update');
            Route::delete('business/{business}/menu/sections/{section}', [MenuSectionController::class, 'destroy'])
                ->name('menu.sections.destroy');

            Route::get('business/{business}/menu/sections/{section}/items', [MenuItemController::class, 'index'])
                ->name('menu.items.index');
            Route::post('business/{business}/menu/sections/{section}/items', [MenuItemController::class, 'store'])
                ->name('menu.items.store');
            Route::post('business/{business}/menu/sections/{section}/items/{item}', [MenuItemController::class, 'update'])
                ->name('menu.items.update');
            Route::delete('business/{business}/menu/sections/{section}/items/{item}', [MenuItemController::class, 'destroy'])
                ->name('menu.items.destroy');
        });
    });
