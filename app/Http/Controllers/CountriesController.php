<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function index(): View
    {
        $countries=Country::all()->load('drivers','teams','circuits');
        return view('pages.countries.index', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        Country::create([
            'name' => $request->name,
            'acronym' => $request->acronym
        ]);
        return to_route('countries.index');
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $country->update([
            'name' => $request->name,
            'acronym' => $request->acronym
        ]);
        return to_route('countries.index');
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        return to_route('countries.index');
    }
}
