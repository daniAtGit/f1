<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use App\Models\Driver;
use App\Models\DriverTeam;
use App\Models\Edition;
use App\Models\EditionCircuit;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditionsController extends Controller
{
    public function index(): View
    {
        $editions=Edition::all();
        return view('pages.editions.index', compact('editions'));
    }

    public function create(): View
    {
        return view('pages.editions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $edition = Edition::create([
            'edition' => $request->edition,
            'year' => $request->year,
            'wikipedia' => $request->wikipedia
        ]);

        return redirect()->route('editions.edit', ['edition' => $edition]);
    }

    public function edit(Edition $edition): View
    {
        $edition = $edition->load('driversTeams','driversTeams.driver','driversTeams.team','circuits','circuits.circuit.country');
        $drivers = Driver::all()->sortBy('name');
        $teams = Team::all()->sortBy('name');
        $circuits = Circuit::all()->load('country')->sortBy('country.name');
        return view('pages.editions.edit', compact('edition', 'drivers', 'teams', 'circuits'));
    }

    public function update(Request $request, Edition $edition): RedirectResponse
    {
        $edition->update([
            'edition' => $request->edition,
            'year' => $request->year,
            'wikipedia' => $request->wikipedia
        ]);

        return redirect()->route('editions.index');
    }

    public function destroy(Edition $edition): RedirectResponse
    {
        $edition->delete();
        return redirect()->route('editions.index');
    }

    public function driverTeamCreate(Request $request): RedirectResponse
    {
        DriverTeam::create([
            'edition_id' => $request->edition_id,
            'driver_id' => $request->driver_id,
            'team_id' => $request->team_id
        ]);

        $edition = Edition::find($request->edition_id);
        //return redirect()->route('editions.edit', ['edition' => $edition]);
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'teams_drivers'
        ]);
    }

    public function circuitCreate(Request $request): RedirectResponse
    {
        EditionCircuit::create([
            'edition_id' => $request->edition_id,
            'circuit_id' => $request->circuit_id,
            'round' => $request->round
        ]);

        $edition = Edition::find($request->edition_id);
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'circuits'
        ]);
    }

    public function circuitEdit($editionId, $editionCircuitId): View
    {
        $editionCircuit = EditionCircuit::find($editionCircuitId);
        $circuits = Circuit::all()->load('country')->sortBy('country.name');
        return view('pages.editions.partials.circuits-edit', compact('editionCircuit','circuits'));
    }

    public function circuitUpdate(Request $request): RedirectResponse
    {
        $editionCircuit = EditionCircuit::find($request->editionCircuitId);
        $editionCircuit->update([
            'circuit_id' => $request->circuit_id,
            'round' => $request->round,
            'date' => $request->date
        ]);
        return redirect()->route('editions.edit', [
            'edition' => $editionCircuit->edition_id,
            'tab' => 'circuits'
        ]);
    }

    public function circuitDestroy($editionId, $editionCircuitId): RedirectResponse
    {
        $editionCircuit = EditionCircuit::find($editionCircuitId);
        $editionCircuit->delete();
        return redirect()->route('editions.edit', [
            'edition' => $editionId,
            'tab' => 'circuits'
        ]);
    }
}
