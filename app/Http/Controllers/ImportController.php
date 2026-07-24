<?php

namespace App\Http\Controllers;

use App\Models\DriverTeam;
use App\Models\Edition;
use App\Models\EditionCircuit;
use App\Models\GridCircuit;
use App\Models\RaceCircuit;
use App\Models\SprintCircuit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            ->withCount(['grid', 'race', 'sprint'])
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
                'existing_imports' => [
                    'grid' => $editionCircuit->grid_count > 0,
                    'race' => $editionCircuit->race_count > 0,
                    'sprint' => $editionCircuit->sprint_count > 0,
                ],
            ]);

        return response()->json([
            'drivers_count' => $edition->driversTeams()->count(),
            'driver_numbers' => $edition->driversTeams()
                ->pluck('number')
                ->map(fn ($number) => (string) $number)
                ->values(),
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
            'confirm_overwrite' => ['nullable', 'boolean'],
        ]);

        $gridData = json_decode($request->grid_data, true);
        $editionCircuit = EditionCircuit::query()
            ->whereKey($request->circuit)
            ->where('edition_id', $request->edition)
            ->firstOrFail();

        $resultModel = match ($request->type) {
            'grid' => GridCircuit::class,
            'race' => RaceCircuit::class,
            'sprint' => SprintCircuit::class,
        };

        $rows = collect($gridData)
            ->filter(fn ($row) => is_array($row) && collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isNotEmpty())
            ->map(fn ($row) => array_map(fn ($value) => trim((string) $value), $row))
            ->values();

        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['grid_data' => 'Inserisci almeno un risultato valido.']);
        }

        $driversTeams = DriverTeam::query()
            ->where('edition_id', $request->edition)
            ->get();

        $numbers = $rows->pluck(1);
        $unknownNumbers = $numbers
            ->filter()
            ->reject(fn ($number) => $driversTeams->contains(fn (DriverTeam $driverTeam) => (string) $driverTeam->number === $number));

        if ($numbers->contains('') || $rows->pluck(0)->contains('') || $unknownNumbers->isNotEmpty()) {
            throw ValidationException::withMessages([
                'grid_data' => 'Verifica i dati: posizione e numero pilota sono obbligatori. Numeri non presenti nell\'edizione: '.$unknownNumbers->unique()->implode(', ').'.',
            ]);
        }

        if ($numbers->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['grid_data' => 'Verifica i dati: un numero pilota compare più di una volta.']);
        }

        $existingResults = $resultModel::where('edition_circuit_id', $editionCircuit->id);
        if ($existingResults->exists() && ! $request->boolean('confirm_overwrite')) {
            throw ValidationException::withMessages(['grid_data' => 'Esistono già risultati per questo circuito e questa tipologia. Conferma la sostituzione per procedere.']);
        }

        DB::transaction(function () use ($rows, $driversTeams, $editionCircuit, $request, $resultModel, $existingResults) {
            if ($request->boolean('confirm_overwrite')) {
                $existingResults->delete();
            }

            foreach ($rows as $row) {
                $number = trim((string) ($row[1] ?? ''));

                // Team names vary between sources; race number is unique within an edition.
                $driverTeam = $driversTeams->first(function (DriverTeam $driverTeam) use ($number) {
                    return $number !== '' && (string) $driverTeam->number === $number;
                });

                if ($driverTeam) {
                    $resultModel::create([
                        'position' => $row[0],
                        'driver_team_id' => $driverTeam->id,
                        'circuit_id' => $editionCircuit->circuit_id,
                        'edition_circuit_id' => $editionCircuit->id,
                        'time' => $row[3] ?: null,
                    ]);
                }
            }
        });

        return redirect()->route('editions.circuit.edit', [
            $editionCircuit->edition_id,
            $editionCircuit->id,
        ]);
    }
}
