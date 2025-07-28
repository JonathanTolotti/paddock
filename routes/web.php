<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPortal\QuoteController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WorkOrderController;
use App\Models\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/clients/{client:uuid}/vehicles', function (Client $client) {
        return $client->vehicles()->orderBy('make')->get();
    })->name('clients.vehicles');

    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('clients', ClientController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('work-orders', WorkOrderController::class);

    Route::get('/services/search', [ServiceController::class, 'search'])->name('services.search');
    Route::resource('services', ServiceController::class);

    Route::post('/work-orders/{workOrder}/services', [WorkOrderController::class, 'addService'])->name('work-orders.services.add');
    Route::delete('/work-orders/{workOrder}/services/{service}', [WorkOrderController::class, 'removeService'])->name('work-orders.services.remove');

    Route::get('/parts/search', [PartController::class, 'search'])->name('parts.search');
    Route::resource('parts', PartController::class);

    Route::post('/work-orders/{workOrder}/parts', [WorkOrderController::class, 'addPart'])->name('work-orders.parts.add');
    Route::delete('/work-orders/{workOrder}/parts/{part}', [WorkOrderController::class, 'removePart'])->name('work-orders.parts.remove');

    Route::patch('/work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus'])->name('work-orders.status.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', UserController::class);

        Route::resource('roles', RoleController::class)->only(['index', 'edit', 'update']);

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    });
});

Route::middleware('signed')->group(function () {
    Route::get('/quote/{workOrder:uuid}', [QuoteController::class, 'show'])
        ->name('quote.view');

    Route::post('/quote/{workOrder:uuid}/approve', [QuoteController::class, 'approve'])
        ->name('quote.approve');

    Route::post('/quote/{workOrder:uuid}/reject', [QuoteController::class, 'reject'])
        ->name('quote.reject');
});



require __DIR__.'/auth.php';
