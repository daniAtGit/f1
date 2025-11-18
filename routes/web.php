<?php

use App\Http\Controllers\CircuitsController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\EditionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('drivers', DriversController::class)->parameters(['drivers'=>'driver']);
    Route::resource('teams', TeamsController::class)->parameters(['teams'=>'team']);
    Route::resource('circuits', CircuitsController::class)->except('show')->parameters(['circuits'=>'circuit']);

    Route::resource('editions', EditionsController::class)->except('show')->parameters(['editions'=>'edition']);
    Route::post('editions-driver-team-create', [EditionsController::class, 'driverTeamCreate'])->name('editions.driver.team.create');
    Route::post('editions-circuit-create', [EditionsController::class, 'circuitCreate'])->name('editions.circuit.create');
    Route::get('editions/{editionId}/circuit/{circuitId}/edit', [EditionsController::class, 'circuitEdit'])->name('editions.circuit.edit');
    Route::get('editions/{editionId}/circuit/{circuitId}/delete', [EditionsController::class, 'circuitDestroy'])->name('editions.circuit.delete');
    Route::post('editions-circuit-update', [EditionsController::class, 'circuitUpdate'])->name('editions.circuit.update');



    Route::resource('countries', CountriesController::class)->except('create','show','edit')->parameters(['countries'=>'country']);
});

require __DIR__.'/auth.php';
