<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create([
            'name' => 'McLaren F1',
            'color' => '#ff8431',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/McLaren_F1'
        ]);

        Team::create([
            'name' => 'Scuderia Ferrari',
            'color' => '#e71725',
            'country_id' => Country::where('name', 'Italy')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Scuderia_Ferrari'
        ]);

        Team::create([
            'name' => 'Red Bull Racing',
            'color' => '#0d202f',
            'country_id' => Country::where('name', 'Austria')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Red_Bull_Racing'
        ]);

        Team::create([
            'name' => 'Mercedes-AMG F1',
            'color' => '#000000',
            'country_id' => Country::where('name', 'Germany')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Mercedes_AMG_F1'
        ]);

        Team::create([
            'name' => 'Aston Martin F1',
            'color' => '#229da2',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Aston_Martin_F1_Team'
        ]);

        Team::create([
            'name' => 'Alpine F1',
            'color' => '#ff89bd',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://en.wikipedia.org/wiki/Alpine_F1_Team'
        ]);

        Team::create([
            'name' => 'Haas F1',
            'color' => '#db291d',
            'country_id' => Country::where('name', 'USA')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Haas_F1_Team'
        ]);

        Team::create([
            'name' => 'Racing Bulls',
            'color' => '#37144e',
            'country_id' => Country::where('name', 'Italy')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Racing_Bulls_F1_Team'
        ]);

        Team::create([
            'name' => 'Williams Racing',
            'color' => '#080e97',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Williams_F1'
        ]);

        Team::create([
            'name' => 'Sauber',
            'color' => '#06e10c',
            'country_id' => Country::where('name', 'Swiss')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Sauber_F1_Team'
        ]);
    }
}
