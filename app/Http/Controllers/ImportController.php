<?php

namespace App\Http\Controllers;

use App\Models\DriverTeam;
use App\Models\Edition;
use App\Models\EditionCircuit;
use App\Models\GridCircuit;
use App\Models\RaceCircuit;
use App\Models\SprintCircuit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        $editions = Edition::all()->sortByDesc('edition');
        return view('pages.import.index', compact('editions'));
    }

    public function circuits(Edition $edition): JsonResponse
    {
        $circuits = $edition->circuits()
            ->with('circuit.country')
            ->orderBy('round')
            ->get()
            ->map(fn ($editionCircuit) => [
                'id' => $editionCircuit->id,
                'round' => $editionCircuit->round,
                'name' => trim(implode(' - ', array_filter([
                    'Round '.$editionCircuit->round,
                    $editionCircuit->circuit?->country?->name,
                    $editionCircuit->circuit?->city,
                    $editionCircuit->circuit?->name,
                ]))),
            ]);

        return response()->json([
            'drivers_count' => $edition->driversTeams()->count(),
            'circuits' => $circuits,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'edition' => ['required', 'exists:editions,id'],
            'circuit' => ['required', 'exists:edition_circuit,id'],
            'type' => ['required', 'in:grid,race,sprint'],
            'grid_data' => ['required', 'json'],
        ]);

        $gridData = json_decode($request->grid_data, true);
        $driversTeams = DriverTeam::query()
            ->where('edition_id', $request->edition)
            ->get();
        $editionCircuit = EditionCircuit::findOrFail($request->circuit);

        $pos=1;
        foreach ($gridData as $row) {
            $number = trim((string) ($row[1] ?? ''));

            // Team names vary between sources; race number is unique within an edition.
            $driverTeam = $driversTeams->first(function (DriverTeam $driverTeam) use ($number) {
                return $number !== '' && (string) $driverTeam->number === $number;
            });

            if($driverTeam) {
                if($request->type == 'grid'){
                    GridCircuit::create([
                        'position' => $pos,
                        'driver_team_id' => $driverTeam->id,
                        'circuit_id' => $editionCircuit->circuit_id,
                        'edition_circuit_id' => $request->circuit,
                        'time' => $row[3] ?? null,
                    ]);
                }

                if($request->type == 'race'){
                    RaceCircuit::create([
                        'position' => $pos,
                        'driver_team_id' => $driverTeam->id,
                        'circuit_id' => $editionCircuit->circuit_id,
                        'edition_circuit_id' => $request->circuit,
                        'time' => $row[3] ?? null,
                    ]);
                }

                if($request->type == 'sprint'){
                    SprintCircuit::create([
                        'position' => $pos,
                        'driver_team_id' => $driverTeam->id,
                        'circuit_id' => $editionCircuit->circuit_id,
                        'edition_circuit_id' => $request->circuit,
                        'time' => $row[3] ?? null,
                    ]);
                }
            }

            $pos++;
        }

        return redirect()->route('editions.circuit.edit', [
            $editionCircuit->edition_id,
            $editionCircuit->id,
        ]);
    }
}
