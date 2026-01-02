<?php

use App\Http\Controllers\Companies\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Rules\PermissionAccessController;
use App\Http\Controllers\Rules\PermissionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::impersonate();
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::middleware('role:admin')->group(function () {
        Route::prefix('access-control')->name('access-control.')->group(function () {
            Route::resource('permissions', PermissionController::class);
            
            Route::get('company-access', [PermissionAccessController::class, 'index'])->name('company-access.index');
            Route::post('company-access/{company}', [PermissionAccessController::class, 'update'])->name('company-access.update');
        });
        
        Route::get('/companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');
        Route::resource('companies', CompanyController::class)->except(['create', 'store', 'show']);
    });

    Route::get('manage-company', function () {
        return "<h1>Tes halaman company dengan general permission: manage-company</h1>";
    })->middleware('company_can:manage-company');
    
    Route::get('dashboard-access', function () {
        return "<h1>Tes halaman company dengan general permission: dashboard-access</h1>";
    })->middleware('company_can:dashboard-access');

    Route::get('view-analytics', function () {
        return "<h1>Tes halaman company dengan unique permission: view-analytics</h1>";
    })->middleware('company_can:view-analytics');
});

require __DIR__.'/settings.php';