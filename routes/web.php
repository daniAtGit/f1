<?php

use App\Http\Controllers\CarsController;
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
    Route::resource('cars', CarsController::class)->except('show')->parameters(['cars'=>'car']);
    Route::resource('circuits', CircuitsController::class)->parameters(['circuits'=>'circuit']);

    Route::resource('editions', EditionsController::class)->except('show')->parameters(['editions'=>'edition']);
    Route::post('editions-show', [EditionsController::class, 'show'])->name('editions.show');

    Route::post('editions-driver-team-create', [EditionsController::class, 'driverTeamCreate'])->name('editions.driver.team.create');
    Route::post('editions-driver-team-cars', [EditionsController::class, 'driverTeamCars'])->name('editions.driver.team.cars');
    Route::post('editions-driver-team-delete', [EditionsController::class, 'driverTeamDelete'])->name('editions.driver.team.delete');
    Route::post('editions-circuit-create', [EditionsController::class, 'circuitCreate'])->name('editions.circuit.create');
    Route::get('editions/{editionId}/circuit/{circuitId}/edit', [EditionsController::class, 'circuitEdit'])->name('editions.circuit.edit');
    Route::get('editions/{editionId}/circuit/{circuitId}/delete', [EditionsController::class, 'circuitDestroy'])->name('editions.circuit.delete');
    Route::post('editions-circuit-update', [EditionsController::class, 'circuitUpdate'])->name('editions.circuit.update');

    Route::post('editions-ranking-team-create', [EditionsController::class, 'rankingTeamsCreate'])->name('editions.ranking.teams.create');
    Route::post('editions-ranking-team-update', [EditionsController::class, 'rankingTeamUpdate'])->name('editions.ranking.team.update');

    Route::post('editions-ranking-driver-create', [EditionsController::class, 'rankingDriversCreate'])->name('editions.ranking.drivers.create');
    Route::post('editions-ranking-driver-add', [EditionsController::class, 'rankingDriversAdd'])->name('editions.ranking.drivers.add');
    Route::post('editions-ranking-driver-update', [EditionsController::class, 'rankingDriverUpdate'])->name('editions.ranking.driver.update');
    Route::post('editions-ranking-driver-delete', [EditionsController::class, 'rankingDriverDelete'])->name('editions.ranking.driver.delete');

    Route::post('editions-circuit-show', [EditionsController::class, 'showEditionCircuit'])->name('editions.circuit.show');

    Route::post('editions-circuit-link-delete', [EditionsController::class, 'circuitLinkDelete'])->name('editions.circuit.link.delete');
    Route::post('editions-circuit-link-title-update', [EditionsController::class, 'circuitLinkTitleUpdate'])->name('editions.circuit.link.title.update');

    Route::post('editions-circuit-sprint-create', [EditionsController::class, 'sprintCircuitCreate'])->name('editions.circuit.sprint.create');
    Route::post('editions-circuit-sprint-delete', [EditionsController::class, 'sprintCircuitDestroy'])->name('editions.circuit.sprint.delete');

    Route::post('editions-circuit-grid-create', [EditionsController::class, 'gridCircuitCreate'])->name('editions.circuit.grid.create');
    Route::post('editions-circuit-grid-delete', [EditionsController::class, 'gridCircuitDestroy'])->name('editions.circuit.grid.delete');

    Route::post('editions-circuit-race-create', [EditionsController::class, 'raceCircuitCreate'])->name('editions.circuit.race.create');
    Route::post('editions-circuit-race-delete', [EditionsController::class, 'raceCircuitDestroy'])->name('editions.circuit.race.delete');

    Route::resource('countries', CountriesController::class)->except('create','show','edit')->parameters(['countries'=>'country']);
});

require __DIR__.'/auth.php';
