<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function index(): View
    {
        $teams = Team::all()->load('country');
        return view('pages.teams.index', compact('teams'));
    }

    public function create(): View
    {
        $countries=Country::all()->sortBy('name');
        return view('pages.teams.create', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        Team::create([
            'name' => $request->name,
            'color' => $request->color,
            'country_id' => $request->country,
            'wikipedia' => $request->wikipedia
        ]);

        return redirect()->route('teams.index');
    }

    public function show(Team $team): View
    {
        dd($team);
    }

    public function edit(Team $team): View
    {
        $countries=Country::all()->sortBy('name');
        return view('pages.teams.edit', compact('team','countries'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $team->update([
            'name' => $request->name,
            'color' => $request->color,
            'country_id' => $request->country,
            'wikipedia' => $request->wikipedia
        ]);

        return redirect()->route('teams.index');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();
        return redirect()->route('teams.index');
    }
}
