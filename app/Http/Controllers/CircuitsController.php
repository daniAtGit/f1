<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CircuitsController extends Controller
{
    public function index(): View
    {
        $circuits = Circuit::all()->load('country')->sortBy('name');
        return view('pages.circuits.index', compact('circuits'));
    }

    public function create(): View
    {
        $countries = Country::all()->sortBy('name');
        return view('pages.circuits.create', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        Circuit::create([
            'name' => $request->name,
            'city' => $request->city,
            'country_id' => $request->country,
            'wikipedia' => $request->wikipedia,
        ]);

        return redirect()->route('circuits.index');
    }

    public function edit(Circuit $circuit): View
    {
        $countries = Country::all()->sortBy('name');
        return view('pages.circuits.edit', compact('circuit','countries'));
    }

    public function update(Request $request, Circuit $circuit): RedirectResponse
    {
        $circuit->update([
            'name' => $request->name,
            'city' => $request->city,
            'country_id' => $request->country,
            'wikipedia' => $request->wikipedia,
        ]);

        return redirect()->route('circuits.index');
    }

    public function destroy(Circuit $circuit): RedirectResponse
    {
        $circuit->delete();
        return redirect()->route('circuits.index');
    }
}
