<?php

use App\Models\Circuit;
use App\Models\Driver;
use App\Models\DriverTeam;
use App\Models\Edition;
use App\Models\EditionCircuit;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->edition = Edition::create(['edition' => '24', 'year' => '1973']);
    $circuit = Circuit::create(['name' => 'Kyalami', 'city' => 'Midrand']);
    $this->editionCircuit = EditionCircuit::create([
        'edition_id' => $this->edition->id,
        'circuit_id' => $circuit->id,
        'round' => 3,
    ]);
    $driver = Driver::create(['name' => 'Peter Revson']);
    $team = Team::create(['name' => 'McLaren Ford']);
    $this->driverTeam = DriverTeam::create([
        'edition_id' => $this->edition->id,
        'driver_id' => $driver->id,
        'team_id' => $team->id,
        'number' => '7',
    ]);
    $this->payload = [
        'edition' => $this->edition->id,
        'circuit' => $this->editionCircuit->id,
        'grid_data' => json_encode([['1', '6', 'Peter Revson', 'McLaren Ford', '1:20.0']]),
    ];
});

it('imports using the edition entry without changing its number', function (string $type, ?string $storedNumber) {
    $this->driverTeam->update(['number' => $storedNumber]);

    // Number 6 may belong to a different driver in the same edition.
    DriverTeam::create([
        'edition_id' => $this->edition->id,
        'driver_id' => Driver::create(['name' => 'Denny Hulme'])->id,
        'team_id' => $this->driverTeam->team_id,
        'number' => '6',
    ]);

    $this->post(route('import.store'), $this->payload + ['type' => $type])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('editions.circuit.edit', [$this->edition->id, $this->editionCircuit->id]));

    $this->assertDatabaseHas($type.'_circuit', [
        'edition_circuit_id' => $this->editionCircuit->id,
        'driver_team_id' => $this->driverTeam->id,
        'position' => '1',
    ]);
    expect($this->driverTeam->fresh()->number)->toBe($storedNumber);
})->with(['grid', 'race', 'sprint'])->with(['matching' => '6', 'different' => '7', 'absent' => null]);

it('prefers the matching number when multiple entries match the driver and team', function () {
    $preferred = $this->driverTeam->replicate();
    $preferred->number = '6';
    $preferred->save();

    $this->post(route('import.store'), $this->payload + ['type' => 'race'])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('race_circuit', ['driver_team_id' => $preferred->id]);
    $this->assertDatabaseCount('race_circuit', 1);
});

it('accepts a number that is absent from all edition entries', function () {
    $this->payload['grid_data'] = json_encode([['1', '99', 'Peter Revson', 'McLaren Ford', '']]);

    $this->post(route('import.store'), $this->payload + ['type' => 'race'])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('race_circuit', ['driver_team_id' => $this->driverTeam->id]);
    expect($this->driverTeam->fresh()->number)->toBe('7');
});

it('rejects ambiguous entries without overwriting existing results', function (?string $secondNumber) {
    $second = $this->driverTeam->replicate();
    $second->number = $secondNumber;
    $second->save();
    if ($secondNumber === '6') {
        $this->driverTeam->update(['number' => '6']);
    }
    $existing = $this->driverTeam->raceCircuits()->create([
        'position' => '2',
        'circuit_id' => $this->editionCircuit->circuit_id,
        'edition_circuit_id' => $this->editionCircuit->id,
    ]);

    $this->post(route('import.store'), $this->payload + ['type' => 'race', 'confirm_overwrite' => true])
        ->assertSessionHasErrors(['grid_data' => 'Verifica i dati: più abbinamenti pilota/squadra corrispondono ai dati indicati. Impossibile scegliere automaticamente: #6 Peter Revson (McLaren Ford).']);

    $this->assertDatabaseHas('race_circuit', ['id' => $existing->id, 'position' => '2']);
    $this->assertDatabaseCount('race_circuit', 1);
})->with(['fallback' => '8', 'matching numbers' => '6']);

it('rejects an unknown driver or team even if the number matches', function (string $driverName, string $teamName) {
    $this->payload['grid_data'] = json_encode([['1', '7', $driverName, $teamName, '']]);

    $this->post(route('import.store'), $this->payload + ['type' => 'race'])
        ->assertSessionHasErrors('grid_data');

    $this->assertDatabaseCount('race_circuit', 0);
})->with([['Jackie Stewart', 'McLaren Ford'], ['Peter Revson', 'Ferrari']]);

it('rejects duplicate rows that resolve to the same entry with different numbers', function () {
    $this->payload['grid_data'] = json_encode([
        ['1', '6', 'Peter Revson', 'McLaren Ford', ''],
        ['2', '7', 'Peter Revson', 'McLaren Ford', ''],
    ]);

    $this->post(route('import.store'), $this->payload + ['type' => 'race'])
        ->assertSessionHasErrors(['grid_data' => 'Verifica i dati: lo stesso pilota compare più di una volta.']);

    $this->assertDatabaseCount('race_circuit', 0);
});
