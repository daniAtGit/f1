<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use App\Models\Driver;
use App\Models\DriverTeam;
use App\Models\Edition;
use App\Models\EditionCircuit;
use App\Models\GridCircuit;
use App\Models\RaceCircuit;
use App\Models\RankingDriver;
use App\Models\RankingTeam;
use App\Models\SprintCircuit;
use App\Models\Team;
use App\Models\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditionsController extends Controller
{
    public function index(): View
    {
        $editions = Edition::all();
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

    public function show(Request $request)
    {
        $edition = Edition::find($request->editionId);

        $standingDrivers = $edition->rankingDrivers->load('driver','driver.country','team');
        $standingTeams = $edition->rankingTeams->load('team');

        return [
            'title' => 'Edition '.$edition->edition.' - '.$edition->year,
            'standingDrivers' => $standingDrivers,
            'standingTeams' => $standingTeams
        ];
    }

    public function edit(Edition $edition): View
    {
        $edition = $edition->load(
            'driversTeams',
            'driversTeams.driver',
            'driversTeams.team',
            'circuits',
            'circuits.videos',
            'circuits.sprint',
            'circuits.sprint.driverTeam',
            'circuits.sprint.driverTeam.driver',
            'circuits.grid',
            'circuits.grid.driverTeam',
            'circuits.grid.driverTeam.driver',
            'circuits.race',
            'circuits.race.driverTeam',
            'circuits.race.driverTeam.driver');
        $drivers = Driver::all()->sortBy('name');
        $teams = Team::all()->sortBy('name');
        $circuits = Circuit::all()->load('country')->sortBy('country.name');

        $rankingTeams = RankingTeam::where('edition_id', $edition->id)->get()->load('team');
        $rankingDrivers = RankingDriver::where('edition_id', $edition->id)->get()->load('team','driver','driver.country');
        $rankingDriversAdd =  DriverTeam::where('edition_id', $edition->id)->get()->load('team','driver','rankingDrivers');

        //dd($rankingDrivers, $rankingDriversAdd);

        return view('pages.editions.edit', compact('edition', 'drivers', 'teams', 'circuits','rankingTeams','rankingDrivers','rankingDriversAdd'));
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
            'team_id' => $request->team_id,
            'car_id' => $request->car_id,
            'number' => $request->number
        ]);

        $edition = Edition::find($request->edition_id);
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'teams_drivers'
        ]);
    }

    public function driverTeamCars(Request $request): mixed
    {
        $team = Team::find($request->team_id);
        $cars = [];
        foreach ($team->cars->sortByDesc('edition.year') as $i => $car) {
            $id = $car->id;
            $name = $car->name." | ".$car->edition->edition."-".$car->edition->year;
            $cars[$i] = [
                'id' => $id,
                'name' => $name
            ];
        }
        return $cars;
    }

    public function driverTeamDelete(Request $request): RedirectResponse
    {
        $driverTeam = DriverTeam::find($request->driver_team_id);
        $driverTeam->delete();

        $edition = Edition::find($request->edition_id);
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
            'round' => $request->round,
            'date' => $request->date
        ]);

        $edition = Edition::find($request->edition_id);
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'circuits'
        ]);
    }

    public function circuitEdit($editionId, $editionCircuitId): View
    {
        $editionCircuit = EditionCircuit::find($editionCircuitId)->load('edition','edition.driversTeams','edition.driversTeams.driver');
        $circuits = Circuit::all()->load('country')->sortBy('country.name');

        $sprints = SprintCircuit::whereHas('driverTeam', function ($q) use ($editionId, $editionCircuitId) {
            $q->where('edition_id', $editionId)->where('edition_circuit_id', $editionCircuitId);
        })->get()->sortBy('position')->load('driverTeam','driverTeam.driver','driverTeam.team');

        $grids = GridCircuit::whereHas('driverTeam', function ($q) use ($editionId, $editionCircuitId) {
            $q->where('edition_id', $editionId)->where('edition_circuit_id', $editionCircuitId);
        })->get()->sortBy('position')->load('driverTeam','driverTeam.driver','driverTeam.team');

        $races = RaceCircuit::whereHas('driverTeam', function ($q) use ($editionId, $editionCircuitId) {
            $q->where('edition_id', $editionId)->where('edition_circuit_id', $editionCircuitId);
        })->get()->sortBy('position')->load('driverTeam','driverTeam.driver','driverTeam.team');

        return view('pages.editions.partials.circuits-edit', compact('editionCircuit','circuits','sprints','grids','races'));
    }

    public function circuitUpdate(Request $request): RedirectResponse
    {
        $editionCircuit = EditionCircuit::find($request->editionCircuitId);
        $editionCircuit->update([
            'circuit_id' => $request->circuit_id,
            'round' => $request->round,
            'date' => $request->date
        ]);

        if($request->altri) {
            foreach ($request->altri as $altro) {
                $video = $editionCircuit->videos()->create(['url' => $altro]);
                $title = $video->getVideoTitle($video->url) ?? 'video';
                $video->update(['title' => $title]);
            }
        }

        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
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

    public function sprintCircuitCreate(Request $request): RedirectResponse
    {
        SprintCircuit::create([
            'position' => $request->position,
            'driver_team_id' => $request->driverTeam_id,
            'circuit_id' => $request->circuit_id,
            'edition_circuit_id' => $request->editionCircuit_id,
            'time' => $request->time,
        ]);

        $editionCircuit = EditionCircuit::find($request->editionCircuit_id);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function sprintCircuitDestroy(Request $request): RedirectResponse
    {
        $sprint = SprintCircuit::find($request->sprint_id);
        $sprint->delete();

        $editionCircuit = EditionCircuit::find($request->editionCircuit_id);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function gridCircuitCreate(Request $request): RedirectResponse
    {
        GridCircuit::create([
            'position' => $request->position,
            'driver_team_id' => $request->driverTeam_id,
            'circuit_id' => $request->circuit_id,
            'edition_circuit_id' => $request->editionCircuit_id,
            'time' => $request->time,
        ]);

        $editionCircuit = EditionCircuit::find($request->editionCircuit_id);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function gridCircuitDestroy(Request $request): RedirectResponse
    {
        $grid = GridCircuit::find($request->grid_id);
        $grid->delete();

        $editionCircuit = EditionCircuit::find($request->editionCircuit_id);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function raceCircuitCreate(Request $request): RedirectResponse
    {
        RaceCircuit::create([
            'position' => $request->position,
            'driver_team_id' => $request->driverTeam_id,
            'circuit_id' => $request->circuit_id,
            'edition_circuit_id' => $request->editionCircuit_id,
            'time' => $request->time,
        ]);

        $editionCircuit = EditionCircuit::find($request->editionCircuit_id);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function raceCircuitDestroy(Request $request): RedirectResponse
    {
        $race = RaceCircuit::find($request->race_id);
        $race->delete();

        $editionCircuit = EditionCircuit::find($request->editionCircuit_id);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function circuitLinkDelete(Request $request): RedirectResponse
    {
        $video = Video::find($request->video_id);
        $editionCircuit = $video->editionCircuit;
        $video->delete();
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function circuitLinkTitleUpdate(Request $request): RedirectResponse
    {
        $video = Video::find($request->video_id);
        $editionCircuit = $video->editionCircuit;
        $video->update(['title' => $request->title]);
        return redirect()->route('editions.circuit.edit', [$editionCircuit->edition_id,$editionCircuit->id]);
    }

    public function showEditionCircuit(Request $request)
    {
        $editionCircuitId = $request->editionCircuitId;
        $editionCircuit = EditionCircuit::find($editionCircuitId);
        $editionId = $editionCircuit->edition_id;

        $grids = GridCircuit::whereHas('driverTeam', function ($q) use ($editionId, $editionCircuitId) {
            $q->where('edition_id', $editionId)->where('edition_circuit_id', $editionCircuitId);
        })->get()->sortBy('position')->load('driverTeam','driverTeam.driver','driverTeam.team');

        $raceResults = RaceCircuit::whereHas('driverTeam', function ($q) use ($editionId, $editionCircuitId) {
            $q->where('edition_id', $editionId)->where('edition_circuit_id', $editionCircuitId);
        })->get()->sortBy('position')->load('driverTeam','driverTeam.driver','driverTeam.team');

        $sprints = SprintCircuit::whereHas('driverTeam', function ($q) use ($editionId, $editionCircuitId) {
            $q->where('edition_id', $editionId)->where('edition_circuit_id', $editionCircuitId);
        })->get()->sortBy('position')->load('driverTeam','driverTeam.driver','driverTeam.team');

        return [
            'title' => $editionCircuit->circuit->name,
            'grids' => $grids,
            'raceResults' => $raceResults,
            'sprints' => $sprints,
        ];
    }

    public function rankingTeamsCreate(Request $request)
    {
        $edition = Edition::find($request->edition_id);

        foreach($edition->driversTeams->unique('team_id') as $driverTeam){
            RankingTeam::create([
                'points' => 0,
                'team_id' => $driverTeam->team_id,
                'edition_id' => $edition->id,
            ]);
        };

        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'teams_ranking'
        ]);
    }

    public function rankingTeamUpdate(Request $request)
    {
        $rankingTeam = RankingTeam::find($request->ranking_team_id);
        $rankingTeam->update([
            'points' => $request->pts,
        ]);
        return redirect()->route('editions.edit', [
            'edition' => $rankingTeam->edition_id,
            'tab' => 'teams_ranking'
        ]);
    }

    public function rankingDriversCreate(Request $request)
    {
        $edition = Edition::find($request->edition_id);
        foreach($edition->driversTeams as $driverTeam){
            RankingDriver::create([
                'points' => 0,
                'edition_id' => $edition->id,
                'team_id' => $driverTeam->team_id,
                'driver_id' => $driverTeam->driver_id,
            ]);
        };
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'drivers_ranking'
        ]);
    }

    public function rankingDriversAdd(Request $request)
    {
        RankingDriver::create([
            'points' => 0,
            'edition_id' => $request->edition_id_add,
            'team_id' => $request->team_id_add,
            'driver_id' => $request->driver_id_add,
        ]);
        $edition = Edition::find($request->edition_id_add);
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'drivers_ranking'
        ]);
    }

    public function rankingDriverUpdate(Request $request)
    {
        $rankingDriver = RankingDriver::find($request->ranking_driver_id);
        $edition = Edition::find($rankingDriver->edition_id);
        $rankingDriver->update([
            'points' => $request->pts,
        ]);
        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'drivers_ranking'
        ]);
    }

    public function rankingDriverDelete(Request $request)
    {
        $rankingDriver = RankingDriver::find($request->ranking_driver_id);
        $edition = Edition::find($rankingDriver->edition_id);
        $rankingDriver->delete();

        return redirect()->route('editions.edit', [
            'edition' => $edition,
            'tab' => 'drivers_ranking'
        ]);
    }
}
