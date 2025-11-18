<?php

namespace App\Http\Controllers;
use App\Models\Country;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;


class DriversController extends Controller
{
    public function index(): View
    {
        $drivers=Driver::all()->load('country');
        return view('pages.drivers.index', compact('drivers'));
    }

    public function create(): View
    {
        $countries=Country::all()->sortBy('name');
        return view('pages.drivers.create', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        Driver::create([
            'name' => $request->name,
            'number' => $request->number,
            'birth_year' => $request->birth_year,
            'country_id' => $request->country,
            'wikipedia' => $request->wikipedia
        ]);

        return redirect()->route('drivers.index');
    }

    public function show(Driver $driver): View
    {
        dd($driver);
    }

    public function edit(Driver $driver): View
    {
        $countries=Country::all()->sortBy('name');
        return view('pages.drivers.edit', compact('driver','countries'));
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $driver->update([
            'name' => $request->name,
            'number' => $request->number,
            'birth_year' => $request->birth_year,
            'country_id' => $request->country,
            'wikipedia' => $request->wikipedia
        ]);

        return redirect()->route('drivers.index');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();
        return to_route('drivers.index');
    }
}
