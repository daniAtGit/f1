<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Driver::create([
            'name' => 'Andrea Kimi Antonelli',
            'birth_year' => '2006',
            'country_id' => Country::where('name', 'Italy')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Andrea_Kimi_Antonelli'
        ]);

        Driver::create([
            'name' => 'Lando Norris',
            'birth_year' => '1999',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Lando_Norris'
        ]);

        Driver::create([
            'name' => 'Oscar Piastri',
            'birth_year' => '2001',
            'country_id' => Country::where('name', 'Australia')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Oscar_Piastri'
        ]);

        Driver::create([
            'name' => 'Charles Leclerc',
            'birth_year' => '1997',
            'country_id' => Country::where('name', 'Monaco')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Charles_Leclerc'
        ]);

        Driver::create([
            'name' => 'Lewis Hamilton',
            'birth_year' => '1985',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Lewis_Hamilton'
        ]);

        Driver::create([
            'name' => 'Max Verstappen',
            'birth_year' => '1997',
            'country_id' => Country::where('name', 'Netherlands')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Max_Verstappen'
        ]);

        Driver::create([
            'name' => 'Yuki Tsunoda',
            'birth_year' => '2000',
            'country_id' => Country::where('name', 'Japan')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Y%C5%ABki_Tsunoda'
        ]);

        Driver::create([
            'name' => 'George Russell',
            'birth_year' => '1998',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/George_Russell_(pilota_automobilistico)'
        ]);

        Driver::create([
            'name' => 'Fernando Alonso',
            'birth_year' => '1981',
            'country_id' => Country::where('name', 'Spain')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Fernando_Alonso'
        ]);

        Driver::create([
            'name' => 'Lance Stroll',
            'birth_year' => '1998',
            'country_id' => Country::where('name', 'Canada')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Lance_Stroll'
        ]);

        Driver::create([
            'name' => 'Pierre Gasly',
            'birth_year' => '1996',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Pierre_Gasly'
        ]);

        Driver::create([
            'name' => 'Jack Doohan',
            'birth_year' => '2003',
            'country_id' => Country::where('name', 'Australia')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Jack_Doohan'
        ]);

        Driver::create([
            'name' => 'Esteban Ocon',
            'birth_year' => '1996',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Esteban_Ocon'
        ]);

        Driver::create([
            'name' => 'Oliver Bearman',
            'birth_year' => '2005',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Oliver_Bearman'
        ]);

        Driver::create([
            'name' => 'Liam Lawson',
            'birth_year' => '2002',
            'country_id' => Country::where('name', 'New Zealand')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Liam_Lawson'
        ]);

        Driver::create([
            'name' => 'Isack Hadjar',
            'birth_year' => '2004',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Isack_Hadjar'
        ]);

        Driver::create([
            'name' => 'Alexander Albon',
            'birth_year' => '1996',
            'country_id' => Country::where('name', 'Thailand')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Alexander_Albon'
        ]);

        Driver::create([
            'name' => 'Carlos Sainz',
            'birth_year' => '1994',
            'country_id' => Country::where('name', 'Spain')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Carlos_Sainz_Jr.'
        ]);

        Driver::create([
            'name' => 'Nico Hulkenberg',
            'birth_year' => '1987',
            'country_id' => Country::where('name', 'Germany')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Nico_H%C3%BClkenberg'
        ]);

        Driver::create([
            'name' => 'Gabriel Bortoleto',
            'birth_year' => '2004',
            'country_id' => Country::where('name', 'Brazil')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Gabriel_Bortoleto'
        ]);
    }
}
