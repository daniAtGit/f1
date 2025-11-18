<?php

namespace Database\Seeders;

use App\Models\Circuit;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CircuitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Circuit::create([
            'name' => 'Melbourne Grand Prix Circuit',
            'city' => 'Melbourne',
            'country_id' => Country::where('name', 'Australia')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_Albert_Park'
        ]);

        Circuit::create([
            'name' => 'Shanghai International Circuit',
            'city' => 'Shanghai',
            'country_id' => Country::where('name', 'China')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Shanghai'
        ]);

        Circuit::create([
            'name' => 'Suzuka International Racing Course',
            'city' => 'Suzuka',
            'country_id' => Country::where('name', 'Japan')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Suzuka'
        ]);

        Circuit::create([
            'name' => 'Barhain International Circuit',
            'city' => 'Sakhir',
            'country_id' => Country::where('name', 'Barhain')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Bahrain_International_Circuit'
        ]);

        Circuit::create([
            'name' => 'Jeddah Corniche Circuit',
            'city' => 'Jeddah',
            'country_id' => Country::where('name', 'Saudi Arabia')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Jeddah_Corniche_Circuit'
        ]);

        Circuit::create([
            'name' => 'Miami international Autodrome',
            'city' => 'Miami',
            'country_id' => Country::where('name', 'USA')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Miami_International_Autodrome'
        ]);

        Circuit::create([
            'name' => 'Autodromo Enzo e Dino Ferrari',
            'city' => 'Imola',
            'country_id' => Country::where('name', 'Italy')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Autodromo_Enzo_e_Dino_Ferrari'
        ]);

        Circuit::create([
            'name' => 'Circuit de Monaco',
            'city' => 'Montecarlo',
            'country_id' => Country::where('name', 'Monaco')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Monte_Carlo'
        ]);

        Circuit::create([
            'name' => 'Circuit de Barcelona- Catalunya',
            'city' => 'Barcellona',
            'country_id' => Country::where('name', 'Spain')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Barcellona-Catalogna'
        ]);

        Circuit::create([
            'name' => 'Circuit Gilles Villeneuve',
            'city' => 'Montreal',
            'country_id' => Country::where('name', 'Canada')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Montr%C3%A9al'
        ]);

        Circuit::create([
            'name' => 'Red Bull Ring',
            'city' => 'Spielberg',
            'country_id' => Country::where('name', 'Austria')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Red_Bull_Ring'
        ]);

        Circuit::create([
            'name' => 'Silverstone Circuit',
            'city' => 'Silverstone',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Silverstone'
        ]);

        Circuit::create([
            'name' => 'Circuit de Spa-Francorchamps',
            'city' => 'Francorchamps',
            'country_id' => Country::where('name', 'Belgium')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Spa-Francorchamps'
        ]);

        Circuit::create([
            'name' => 'Hungaroring',
            'city' => 'Mogyrod',
            'country_id' => Country::where('name', 'Hungary')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Hungaroring'
        ]);

        Circuit::create([
            'name' => 'Circuit Zandvoort',
            'city' => 'Zandvoort',
            'country_id' => Country::where('name', 'Netherlands')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Zandvoort'
        ]);

        Circuit::create([
            'name' => 'Autodromo Nazionale di Monza',
            'city' => 'Monza',
            'country_id' => Country::where('name', 'Italy')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Autodromo_nazionale_di_Monza'
        ]);

        Circuit::create([
            'name' => 'Baku City Circuit',
            'city' => 'Baku',
            'country_id' => Country::where('name', 'Azerbaijan')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Baku'
        ]);

        Circuit::create([
            'name' => 'Marina Bay Street Circuit',
            'city' => 'Singapore',
            'country_id' => Country::where('name', 'Singapore')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Singapore_Street_Circuit'
        ]);

        Circuit::create([
            'name' => 'Circuit of the Americas',
            'city' => 'Austin',
            'country_id' => Country::where('name', 'USA')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_delle_Americhe'
        ]);

        Circuit::create([
            'name' => 'Autodromo Hernanos Rodriguez',
            'city' => 'Mexico City',
            'country_id' => Country::where('name', 'Mexico')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Autodromo_Hermanos_Rodr%C3%ADguez'
        ]);

        Circuit::create([
            'name' => 'Autodromo José Carlos Pace',
            'city' => 'Interlagos',
            'country_id' => Country::where('name', 'Brazil')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Interlagos'
        ]);

        Circuit::create([
            'name' => 'Las Vegas Street Circuit',
            'city' => 'Las Vegas',
            'country_id' => Country::where('name', 'USA')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Las_Vegas_Strip_Circuit'
        ]);

        Circuit::create([
            'name' => 'Lusail International Circuit',
            'city' => 'Lusail',
            'country_id' => Country::where('name', 'Qatar')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Lusail'
        ]);

        Circuit::create([
            'name' => 'Yas Marina Circuit',
            'city' => 'Yas Island',
            'country_id' => Country::where('name', 'United Arab Emirates')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Circuito_di_Yas_Marina'
        ]);
    }
}
