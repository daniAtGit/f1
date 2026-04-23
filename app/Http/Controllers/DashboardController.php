<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\EditionCircuit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', $this->buildDashboardData($request));
    }

    public function welcome(Request $request): View
    {
        return view('welcome', $this->buildDashboardData($request));
    }

    private function buildDashboardData(Request $request): array
    {
        $editions = Edition::all()->sortByDesc('year');

        $selectedEditionId = $request->query('edition');
        $edition = $selectedEditionId ? Edition::query()->find($selectedEditionId) : null;
        $edition ??= Edition::query()->orderByDesc('year')->first();

        $prevRace = EditionCircuit::query()
            ->with('circuit.country')
            ->whereHas('race')
            ->whereDate('date', '<=', today())
            ->orderByDesc('date')
            ->orderByDesc('round')
            ->first();

        $currentRace = EditionCircuit::query()
            ->with('circuit.country')
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('round')
            ->first();

        $nextRace = EditionCircuit::query()
            ->with('circuit.country')
            ->whereDate('date', '>=', today())
            ->when($currentRace, function ($query) use ($currentRace) {
                $query->where(function ($subQuery) use ($currentRace) {
                    $subQuery
                        ->whereDate('date', '>', $currentRace->date)
                        ->orWhere(function ($sameDayQuery) use ($currentRace) {
                            $sameDayQuery
                                ->whereDate('date', $currentRace->date)
                                ->where('round', '>', $currentRace->round);
                        });
                });
            })
            ->orderBy('date')
            ->orderBy('round')
            ->first();

        $standingDrivers = $edition?->rankingDrivers()
            ->with([
                'driver.country',
                'driver.driverTeams' => function ($query) use ($edition) {
                    $query->when($edition, fn ($q) => $q->where('edition_id', $edition->id));
                },
                'team',
            ])
            ->orderByRaw('CAST(points AS UNSIGNED) DESC')
            ->get();
        $standingTeams = $edition?->rankingTeams()->with('team')->orderByRaw('CAST(points AS UNSIGNED) DESC')->get();

        return compact('editions', 'edition', 'prevRace', 'currentRace', 'nextRace', 'standingDrivers', 'standingTeams');
    }
}
