<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Edition;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarsController extends Controller
{
    public function index(): View
    {
        $cars = Car::all();
        return view('pages.cars.index', compact('cars'));
    }

    public function create(): View
    {
        $teams = Team::all()->sortBy('name');
        $editions = Edition::all()->sortByDesc('year');
        return view('pages.cars.create', compact('teams','editions'));
    }

    public function store(Request $request): RedirectResponse
    {
        Car::create([
            'name' => $request->name,
            'team_id' => $request->team_id,
            'edition_id' => $request->edition_id,
        ]);
        return redirect()->route('cars.index');
    }

    public function edit(Car $car): View
    {
        $teams = Team::all()->sortBy('name');
        $editions = Edition::all()->sortByDesc('year');
        return view('pages.cars.edit', compact('car','teams','editions'));
    }

    public function update(Request $request, Car $car): RedirectResponse
    {
        $car->update([
            'name' => $request->name,
            'team_id' => $request->team_id,
            'edition_id' => $request->edition_id,
        ]);
        return redirect()->route('cars.index');
    }

    public function destroy(Car $car): RedirectResponse
    {
        $car->delete();
        return redirect()->route('cars.index');
    }
}
