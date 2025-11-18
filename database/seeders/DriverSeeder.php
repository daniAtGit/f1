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
            'number' => '77',
            'birth_year' => '2006',
            'country_id' => Country::where('name', 'Italy')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Andrea_Kimi_Antonelli'
        ]);

        Driver::create([
            'name' => 'Lando Norris',
            'number' => '4',
            'birth_year' => '1999',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Lando_Norris'
        ]);

        Driver::create([
            'name' => 'Oscar Piastri',
            'number' => '81',
            'birth_year' => '2001',
            'country_id' => Country::where('name', 'Australia')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Oscar_Piastri'
        ]);

        Driver::create([
            'name' => 'Charles Leclerc',
            'number' => '16',
            'birth_year' => '1997',
            'country_id' => Country::where('name', 'Monaco')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Charles_Leclerc'
        ]);

        Driver::create([
            'name' => 'Lewis Hamilton',
            'number' => '44',
            'birth_year' => '1985',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Lewis_Hamilton'
        ]);

        Driver::create([
            'name' => 'Max Verstappen',
            'number' => '1',
            'birth_year' => '1997',
            'country_id' => Country::where('name', 'Netherlands')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Max_Verstappen'
        ]);

        Driver::create([
            'name' => 'Yuki Tsunoda',
            'number' => '22',
            'birth_year' => '2000',
            'country_id' => Country::where('name', 'Japan')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Y%C5%ABki_Tsunoda'
        ]);

        Driver::create([
            'name' => 'George Russell',
            'number' => '63',
            'birth_year' => '1998',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/George_Russell_(pilota_automobilistico)'
        ]);

        Driver::create([
            'name' => 'Fernando Alonso',
            'number' => '14',
            'birth_year' => '1981',
            'country_id' => Country::where('name', 'Spain')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Fernando_Alonso'
        ]);

        Driver::create([
            'name' => 'Lance Stroll',
            'number' => '18',
            'birth_year' => '1998',
            'country_id' => Country::where('name', 'Canada')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Lance_Stroll'
        ]);

        Driver::create([
            'name' => 'Gasly',
            'number' => '10',
            'birth_year' => '1996',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Pierre_Gasly'
        ]);

        Driver::create([
            'name' => 'Jack Doohan',
            'number' => '7',
            'birth_year' => '2003',
            'country_id' => Country::where('name', 'Australia')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Jack_Doohan'
        ]);

        Driver::create([
            'name' => 'Esteban Ocon',
            'number' => '31',
            'birth_year' => '1996',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Esteban_Ocon'
        ]);

        Driver::create([
            'name' => 'Oliver Bearman',
            'number' => '87',
            'birth_year' => '2005',
            'country_id' => Country::where('name', 'United Kingdom')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Oliver_Bearman'
        ]);

        Driver::create([
            'name' => 'Liam Lawson',
            'number' => '30',
            'birth_year' => '2002',
            'country_id' => Country::where('name', 'New Zealand')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Liam_Lawson'
        ]);

        Driver::create([
            'name' => 'Isack Hadjar',
            'number' => '6',
            'birth_year' => '2004',
            'country_id' => Country::where('name', 'France')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Isack_Hadjar'
        ]);

        Driver::create([
            'name' => 'Alexander Albon',
            'number' => '23',
            'birth_year' => '1996',
            'country_id' => Country::where('name', 'Thailand')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Alexander_Albon'
        ]);

        Driver::create([
            'name' => 'Carlos Sainz',
            'number' => '55',
            'birth_year' => '1994',
            'country_id' => Country::where('name', 'Spain')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Carlos_Sainz_Jr.'
        ]);

        Driver::create([
            'name' => 'Nico Hulkenberg',
            'number' => '27',
            'birth_year' => '1987',
            'country_id' => Country::where('name', 'Germany')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Nico_H%C3%BClkenberg'
        ]);

        Driver::create([
            'name' => 'Gabriel Bortoleto',
            'number' => '5',
            'birth_year' => '2004',
            'country_id' => Country::where('name', 'Brazil')->first()->id,
            'wikipedia' => 'https://it.wikipedia.org/wiki/Gabriel_Bortoleto'
        ]);
    }
}
