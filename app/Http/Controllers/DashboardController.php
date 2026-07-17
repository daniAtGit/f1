<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use App\Models\Driver;
use App\Models\DriverTeam;
use App\Models\Edition;
use App\Models\EditionCircuit;
use App\Models\GridCircuit;
use App\Models\RaceCircuit;
use App\Models\SprintCircuit;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
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
            //->whereHas('race')
            ->whereDate('date', '<', today())
            ->orderByDesc('date')
            ->orderByDesc('round')
            ->first()
            ->load('videos');

        $currentRace = EditionCircuit::query()
            ->with('circuit.country')
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('round')
            ->first()
            ->load('videos');

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

    public function driver(Driver $driver): View
    {
        $editions = Edition::query()
            ->with(['rankingDrivers' => fn ($query) => $query->orderByRaw('CAST(points AS UNSIGNED) DESC')])
            ->orderByDesc('year')
            ->get();
        $selectedEditionId = request()->query('edition');
        $edition = $selectedEditionId
            ? $editions->firstWhere('id', $selectedEditionId)
            : null;
        $edition ??= $editions->first();
        $editionYearsById = $editions->pluck('year', 'id');

        $driver->load([
            'driverTeams.team',
            'gridCircuits.driverTeam.team',
            'gridCircuits.editionCircuit.circuit.country',
            'RaceCircuits.driverTeam.team',
            'RaceCircuits.editionCircuit.circuit.country',
            'SprintCircuits.driverTeam.team',
            'SprintCircuits.editionCircuit.circuit.country',
        ]);

        $driverImageUrl = $driver->getImgDriverFromGoogle('racing driver');

        $results = collect([
            'grid' => $driver->gridCircuits->map(fn (GridCircuit $result) => [
                'type' => 'grid',
                'result' => $result,
            ]),
            'race' => $driver->RaceCircuits->map(fn (RaceCircuit $result) => [
                'type' => 'race',
                'result' => $result,
            ]),
            'sprint' => $driver->SprintCircuits->map(fn (SprintCircuit $result) => [
                'type' => 'sprint',
                'result' => $result,
            ]),
        ])->flatten(1);

        $driverNumber = $edition
            ? $driver->driverTeams->firstWhere('edition_id', $edition->id)?->number
            : $driver->driverTeams->last()?->number;
        $editionDriverTeam = $edition
            ? $driver->driverTeams->firstWhere('edition_id', $edition->id)
            : null;

        $poleCount = $driver->gridCircuits->where('position', 1)->count();
        $raceCount = $driver->RaceCircuits->where('position', 1)->count();
        $sprintCount = $driver->SprintCircuits->where('position', 1)->count();

        $editionRankingDrivers = $edition?->rankingDrivers ?? collect();

        $editionDriverRanking = $editionRankingDrivers->firstWhere('driver_id', $driver->id);

        $editionPoints = (int) ($editionDriverRanking?->points ?? 0);
        $editionPosition = $editionDriverRanking
            ? $editionRankingDrivers->search(fn ($rankingDriver) => $rankingDriver->id === $editionDriverRanking->id) + 1
            : null;

        $driverStandingsHistory = $editions
            ->sortBy('year')
            ->map(function (Edition $historyEdition) use ($driver) {
                $rankingDriver = $historyEdition->rankingDrivers->firstWhere('driver_id', $driver->id);

                if (!$rankingDriver) {
                    return null;
                }

                return [
                    'year' => (int) $historyEdition->year,
                    'position' => $historyEdition->rankingDrivers->search(
                        fn ($editionRankingDriver) => $editionRankingDriver->id === $rankingDriver->id
                    ) + 1,
                    'points' => (int) $rankingDriver->points,
                ];
            })
            ->filter()
            ->values();

        if ($edition) {
            $results = $results->filter(fn (array $item) => $item['result']->editionCircuit?->edition_id === $edition->id);
        }

        $editionRacePlacements = $driver->RaceCircuits
            ->filter(fn (RaceCircuit $result) => $result->editionCircuit?->edition_id === $edition?->id)
            ->sortBy(fn (RaceCircuit $result) => $result->editionCircuit?->round ?? PHP_INT_MAX)
            ->values()
            ->map(fn (RaceCircuit $result) => [
                'round' => $result->editionCircuit?->round,
                'position' => (int) $result->position,
                'circuitName' => $result->editionCircuit?->circuit?->name,
                'countryName' => $result->editionCircuit?->circuit?->country?->name,
                'flagIconUrl' => $result->editionCircuit?->circuit?->country?->flag_icon_url,
                'teamColor' => $result->driverTeam?->team?->color,
            ]);

        $editionRaceLineColor = $editionDriverTeam?->team?->color
            ?? $editionRacePlacements->first()['teamColor']
            ?? '#0d6efd';

        $resultsByYear = $results
            ->groupBy(fn (array $item) => $editionYearsById->get($item['result']->editionCircuit?->edition_id, 0))
            ->sortKeysDesc()
            ->map(function ($yearResults) use ($driver, $editionYearsById) {
                return $yearResults
                    ->groupBy(fn (array $item) => $item['result']->edition_circuit_id)
                    ->map(function ($circuitResults) use ($driver, $editionYearsById) {
                        $firstResult = $circuitResults->first()['result'];
                        $editionCircuit = $firstResult->editionCircuit;

                        return [
                            'circuitId' => $editionCircuit?->circuit?->id,
                            'circuitName' => $editionCircuit?->circuit?->name,
                            'countryName' => $editionCircuit?->circuit?->country?->name,
                            'countryFlagIconUrl' => $editionCircuit?->circuit?->country?->flag_icon_url,
                            'city' => $editionCircuit?->circuit?->city,
                            'round' => $editionCircuit?->round ?? 0,
                            'year' => $editionYearsById->get($editionCircuit?->edition_id, 0),
                            'sessions' => collect([
                                [
                                    'type' => 'grid',
                                    'results' => $circuitResults
                                        ->where('type', 'grid')
                                        ->sortBy(fn (array $item) => $item['result']->position)
                                        ->map(function (array $item) use ($driver) {
                                            $driverTeam = $item['result']->driverTeam;
                                            $team = $driverTeam?->team;

                                            return [
                                                'number' => $driverTeam?->number,
                                            'driverName' => $driver->name,
                                                'teamId' => $team?->id,
                                                'teamName' => $team?->name,
                                                'teamColor' => $team?->color,
                                                'position' => $item['result']->position,
                                            ];
                                        })
                                        ->values(),
                                ],
                                [
                                    'type' => 'race',
                                    'results' => $circuitResults
                                        ->where('type', 'race')
                                        ->sortBy(fn (array $item) => $item['result']->position)
                                        ->map(function (array $item) use ($driver) {
                                            $driverTeam = $item['result']->driverTeam;
                                            $team = $driverTeam?->team;

                                            return [
                                                'number' => $driverTeam?->number,
                                            'driverName' => $driver->name,
                                                'teamId' => $team?->id,
                                                'teamName' => $team?->name,
                                                'teamColor' => $team?->color,
                                                'position' => $item['result']->position,
                                            ];
                                        })
                                        ->values(),
                                ],
                                [
                                    'type' => 'sprint',
                                    'results' => $circuitResults
                                        ->where('type', 'sprint')
                                        ->sortBy(fn (array $item) => $item['result']->position)
                                        ->map(function (array $item) use ($driver) {
                                            $driverTeam = $item['result']->driverTeam;
                                            $team = $driverTeam?->team;

                                            return [
                                                'number' => $driverTeam?->number,
                                            'driverName' => $driver->name,
                                                'teamId' => $team?->id,
                                                'teamName' => $team?->name,
                                                'teamColor' => $team?->color,
                                                'position' => $item['result']->position,
                                            ];
                                        })
                                        ->values(),
                                ],
                            ])->filter(fn (array $session) => $session['results']->isNotEmpty())->values(),
                        ];
                    })
                    ->sortBy('round')
                    ->values();
            });

        return view('driver', compact('driver', 'driverImageUrl', 'editions', 'edition', 'editionPoints', 'editionPosition', 'driverNumber', 'poleCount', 'raceCount', 'sprintCount', 'resultsByYear', 'driverStandingsHistory', 'editionRacePlacements', 'editionRaceLineColor'));
    }

    public function driverStats(Request $request, Driver $driver): View
    {
        $editions = Edition::query()
            ->orderByDesc('year')
            ->get();

        $selectedEditionId = $request->query('edition');
        $edition = $selectedEditionId
            ? $editions->firstWhere('id', $selectedEditionId)
            : null;
        $edition ??= $editions->first();

        $compareIds = collect($request->query('compare', []))
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->reject(fn ($id) => $id === (string) $driver->id)
            ->unique()
            ->values();

        $selectedDriverIds = $compareIds
            ->prepend((string) $driver->id)
            ->values();

        $drivers = Driver::query()
            ->whereIn('id', $selectedDriverIds)
            ->with([
                'driverTeams.team',
                'RaceCircuits.driverTeam.team',
                'RaceCircuits.editionCircuit.circuit.country',
            ])
            ->get()
            ->sortBy(fn (Driver $item) => array_search((string) $item->id, $selectedDriverIds->all(), true))
            ->values();

        $availableDrivers = Driver::query()
            ->when(
                $edition,
                fn ($query) => $query->whereHas(
                    'driverTeams',
                    fn ($driverTeamQuery) => $driverTeamQuery->where('edition_id', $edition->id)
                )
            )
            ->orderBy('name')
            ->get(['id', 'name']);

        $editionCircuits = $edition
            ? $edition->circuits()
                ->with('circuit.country')
                ->orderBy('round')
                ->get()
            : collect();

        $chartRounds = $editionCircuits
            ->map(fn (EditionCircuit $editionCircuit) => [
                'round' => (int) $editionCircuit->round,
                'circuitName' => $editionCircuit->circuit?->name,
                'countryName' => $editionCircuit->circuit?->country?->name,
                'flagIconUrl' => $editionCircuit->circuit?->country?->flag_icon_url,
            ])
            ->values();

        $chartSeries = $drivers
            ->map(function (Driver $selectedDriver, int $index) use ($edition, $chartRounds) {
                $editionDriverTeam = $edition
                    ? $selectedDriver->driverTeams->firstWhere('edition_id', $edition->id)
                    : null;

                $placements = $selectedDriver->RaceCircuits
                    ->filter(fn (RaceCircuit $result) => $result->editionCircuit?->edition_id === $edition?->id)
                    ->sortBy(fn (RaceCircuit $result) => $result->editionCircuit?->round ?? PHP_INT_MAX)
                    ->values()
                    ->map(fn (RaceCircuit $result) => [
                        'round' => (int) ($result->editionCircuit?->round ?? 0),
                        'position' => (int) $result->position,
                        'teamColor' => $result->driverTeam?->team?->color,
                    ]);

                return [
                    'driverId' => $selectedDriver->id,
                    'driverName' => $selectedDriver->name,
                    'lineColor' => $editionDriverTeam?->team?->color
                        ?? $placements->first()['teamColor']
                        ?? $this->comparisonLineColor($index),
                    'placements' => $placements,
                    'hasResults' => $placements->isNotEmpty(),
                    'roundsCovered' => $chartRounds->pluck('round')->values(),
                ];
            })
            ->values();

        $chartMaxPosition = max(
            1,
            (int) $chartSeries
                ->flatMap(fn (array $series) => collect($series['placements'])->pluck('position'))
                ->max()
        );

        return view('driver-stats', compact(
            'driver',
            'editions',
            'edition',
            'compareIds',
            'drivers',
            'availableDrivers',
            'chartRounds',
            'chartSeries',
            'chartMaxPosition'
        ));
    }

    public function team(Team $team): View
    {
        $editions = Edition::all()->sortByDesc('year');
        $latestEdition = Edition::query()->orderByDesc('year')->first();
        $teamDriverTeamIds = \App\Models\DriverTeam::query()
            ->where('team_id', $team->id)
            ->pluck('id');

        $selectedEditionId = request()->query('edition');
        $edition = $selectedEditionId ? Edition::query()->find($selectedEditionId) : null;
        $edition ??= Edition::query()->orderByDesc('year')->first();

        $results = collect([
            'grid' => GridCircuit::query()
                ->whereIn('driver_team_id', $teamDriverTeamIds)
                ->with([
                    'driverTeam.driver',
                    'editionCircuit.edition',
                    'editionCircuit.circuit.country',
                ])
                ->get()
                ->map(fn (GridCircuit $result) => [
                    'type' => 'grid',
                    'result' => $result,
                ]),
            'race' => RaceCircuit::query()
                ->whereIn('driver_team_id', $teamDriverTeamIds)
                ->with([
                    'driverTeam.driver',
                    'editionCircuit.edition',
                    'editionCircuit.circuit.country',
                ])
                ->get()
                ->map(fn (RaceCircuit $result) => [
                    'type' => 'race',
                    'result' => $result,
                ]),
            'sprint' => SprintCircuit::query()
                ->whereIn('driver_team_id', $teamDriverTeamIds)
                ->with([
                    'driverTeam.driver',
                    'editionCircuit.edition',
                    'editionCircuit.circuit.country',
                ])
                ->get()
                ->map(fn (SprintCircuit $result) => [
                    'type' => 'sprint',
                    'result' => $result,
                ]),
        ])->flatten(1);

        $selectedRankingTeams = $edition
            ? $edition->rankingTeams()
                ->orderByRaw('CAST(points AS UNSIGNED) DESC')
                ->get()
            : collect();

        $selectedTeamRanking = $selectedRankingTeams->firstWhere('team_id', $team->id);

        $editionPoints = (int) ($selectedTeamRanking?->points ?? 0);
        $editionPosition = $selectedTeamRanking
            ? $selectedRankingTeams->search(fn ($rankingTeam) => $rankingTeam->id === $selectedTeamRanking->id) + 1
            : null;

        if ($edition) {
            $results = $results->filter(fn (array $item) => $item['result']->editionCircuit?->edition?->id === $edition->id);
        }

        $resultsByYear = $results
            ->groupBy(fn (array $item) => $item['result']->editionCircuit?->edition?->year ?? 0)
            ->sortKeysDesc()
            ->map(function ($yearResults) {
                return $yearResults
                    ->groupBy(fn (array $item) => $item['result']->edition_circuit_id)
                    ->map(function ($circuitResults) {
                        $firstResult = $circuitResults->first()['result'];
                        $editionCircuit = $firstResult->editionCircuit;

                        return [
                            'circuitId' => $editionCircuit?->circuit?->id,
                            'circuitName' => $editionCircuit?->circuit?->name,
                            'countryName' => $editionCircuit?->circuit?->country?->name,
                            'city' => $editionCircuit?->circuit?->city,
                            'round' => $editionCircuit?->round ?? 0,
                            'year' => $editionCircuit?->edition?->year,
                            'sessions' => collect([
                                [
                                    'type' => 'grid',
                                    'results' => $circuitResults
                                        ->where('type', 'grid')
                                        ->sortBy(fn (array $item) => $item['result']->position)
                                        ->map(fn (array $item) => [
                                            'number' => $item['result']->driverTeam->number,
                                            'driverId' => $item['result']->driverTeam->driver->id,
                                            'driverName' => $item['result']->driverTeam->driver->name,
                                            'position' => $item['result']->position,
                                        ])
                                        ->values(),
                                ],
                                [
                                    'type' => 'race',
                                    'results' => $circuitResults
                                        ->where('type', 'race')
                                        ->sortBy(fn (array $item) => $item['result']->position)
                                        ->map(fn (array $item) => [
                                            'number' => $item['result']->driverTeam->number,
                                            'driverId' => $item['result']->driverTeam->driver->id,
                                            'driverName' => $item['result']->driverTeam->driver->name,
                                            'position' => $item['result']->position,
                                        ])
                                        ->values(),
                                ],
                                [
                                    'type' => 'sprint',
                                    'results' => $circuitResults
                                        ->where('type', 'sprint')
                                        ->sortBy(fn (array $item) => $item['result']->position)
                                        ->map(fn (array $item) => [
                                            'number' => $item['result']->driverTeam->number,
                                            'driverId' => $item['result']->driverTeam->driver->id,
                                            'driverName' => $item['result']->driverTeam->driver->name,
                                            'position' => $item['result']->position,
                                        ])
                                        ->values(),
                                ],
                            ])->filter(fn (array $session) => $session['results']->isNotEmpty())->values(),
                        ];
                    })
                    ->sortBy('round')
                    ->values();
            });

        return view('team', compact('team', 'editions', 'edition', 'editionPoints', 'editionPosition', 'resultsByYear'));
    }

    public function edition(Edition $edition): View
    {
        $editions = Edition::all()->sortByDesc('year');

        $edition->load([
            'circuits.circuit.country',
            'circuits.videos',
            'circuits.grid.driverTeam.driver',
            'circuits.grid.driverTeam.team',
            'circuits.race.driverTeam.driver',
            'circuits.race.driverTeam.team',
            'circuits.sprint.driverTeam.driver',
            'circuits.sprint.driverTeam.team',
        ]);

        $editionCircuits = $edition->circuits
            ->sortBy('round')
            ->values()
            ->map(function (EditionCircuit $editionCircuit) {
                return [
                    'id' => $editionCircuit->circuit->id,
                    'round' => $editionCircuit->round,
                    'date' => $editionCircuit->date?->format('d/m/Y'),
                    'country' => $editionCircuit?->circuit?->country,
                    'circuitName' => $editionCircuit?->circuit?->name,
                    'countryName' => $editionCircuit?->circuit?->country?->name,
                    'city' => $editionCircuit?->circuit?->city,
                    'gridResults' => $editionCircuit->grid
                        ->sortBy(fn (GridCircuit $result) => $result->position)
                        ->map(fn (GridCircuit $result) => [
                            'number' => $result->driverTeam->number,
                            'driverName' => $result->driverTeam->driver->name,
                            'teamName' => $result->driverTeam->team->name,
                            'teamColor' => $result->driverTeam->team->color,
                            'position' => $result->position,
                        ])
                        ->values(),
                    'raceResults' => $editionCircuit->race
                        ->sortBy(fn (RaceCircuit $result) => $result->position)
                        ->map(fn (RaceCircuit $result) => [
                            'number' => $result->driverTeam->number,
                            'driverName' => $result->driverTeam->driver->name,
                            'teamName' => $result->driverTeam->team->name,
                            'teamColor' => $result->driverTeam->team->color,
                            'position' => $result->position,
                        ])
                        ->values(),
                    'sprintResults' => $editionCircuit->sprint
                        ->sortBy(fn (SprintCircuit $result) => $result->position)
                        ->map(fn (SprintCircuit $result) => [
                            'number' => $result->driverTeam->number,
                            'driverName' => $result->driverTeam->driver->name,
                            'teamName' => $result->driverTeam->team->name,
                            'teamColor' => $result->driverTeam->team->color,
                            'position' => $result->position,
                        ])
                        ->values(),
                    'videos' => $editionCircuit->videos
                        ->sortBy('title')
                        ->map(fn ($video) => [
                            'title' => $video->title,
                            'url' => $video->url,
                        ])
                        ->values(),
                ];
            });

        return view('edition', compact('editions', 'edition', 'editionCircuits'));
    }

    public function circuit(Circuit $circuit): View
    {
        $circuit->load('country');

        $poleResults = $this->circuitFirstPlaceResults(GridCircuit::class, $circuit);
        $raceResults = $this->circuitFirstPlaceResults(RaceCircuit::class, $circuit);
        $sprintResults = $this->circuitFirstPlaceResults(SprintCircuit::class, $circuit);
        $driverTeamsById = DriverTeam::with('driver.country')
            ->whereIn('id', $poleResults
                ->merge($raceResults)
                ->merge($sprintResults)
                ->pluck('driver_team_id')
                ->unique()
                ->values())
            ->get()
            ->keyBy('id');

        $poleDrivers = $this->circuitFirstPlaceStandings($poleResults, $driverTeamsById);
        $raceDrivers = $this->circuitFirstPlaceStandings($raceResults, $driverTeamsById);
        $sprintDrivers = $this->circuitFirstPlaceStandings($sprintResults, $driverTeamsById);

        return view('circuit', compact('circuit', 'poleDrivers', 'raceDrivers', 'sprintDrivers'));
    }

    private function circuitFirstPlaceResults(string $resultModel, Circuit $circuit)
    {
        return $resultModel::query()
            ->where('circuit_id', $circuit->id)
            ->where('position', 1)
            ->get();
    }

    private function comparisonLineColor(int $index): string
    {
        return collect([
            '#0d6efd',
            '#dc3545',
            '#198754',
            '#fd7e14',
            '#6f42c1',
            '#20c997',
            '#212529',
            '#d63384',
        ])->get($index % 8, '#0d6efd');
    }

    private function circuitFirstPlaceStandings($results, $driverTeamsById)
    {
        return $results
            ->each(fn ($result) => $result->setRelation(
                'driverTeam',
                $driverTeamsById->get($result->driver_team_id)
            ))
            ->groupBy(fn ($result) => $result->driverTeam?->driver?->id)
            ->map(function ($results) {
                $firstResult = $results->first();

                return [
                    'driver' => $firstResult->driverTeam->driver,
                    'firstPlaces' => $results->count(),
                ];
            })
            ->sort(function (array $a, array $b) {
                if ($a['firstPlaces'] !== $b['firstPlaces']) {
                    return $b['firstPlaces'] <=> $a['firstPlaces'];
                }

                return $a['driver']->name <=> $b['driver']->name;
            })
            ->values()
            ->map(function (array $item, int $index) {
                return [
                    'position' => $index + 1,
                    'driver' => $item['driver'],
                    'firstPlaces' => $item['firstPlaces'],
                ];
            });
    }

}
