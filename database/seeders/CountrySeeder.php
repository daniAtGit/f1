<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::create(['name' => 'Argentina','acronym'=>'ARG']);
        Country::create(['name' => 'Australia','acronym'=>'AUS']);
        Country::create(['name' => 'Austria','acronym'=>'AUT']);
        Country::create(['name' => 'Azerbaijan','acronym'=>'AZE']);
        Country::create(['name' => 'Barhain','acronym'=>'BHR']);
        Country::create(['name' => 'Belgium','acronym'=>'BEL']);
        Country::create(['name' => 'Brazil','acronym'=>'BRA']);
        Country::create(['name' => 'Canada','acronym'=>'CAN']);
        Country::create(['name' => 'China','acronym'=>'CHN']);
        Country::create(['name' => 'Hungary','acronym'=>'HUN']);
        Country::create(['name' => 'France','acronym'=>'FRA']);
        Country::create(['name' => 'Germany','acronym'=>'DEU']);
        Country::create(['name' => 'Italy','acronym'=>'ITA']);
        Country::create(['name' => 'Japan','acronym'=>'JPN']);
        Country::create(['name' => 'Mexico','acronym'=>'MEX']);
        Country::create(['name' => 'Monaco','acronym'=>'MCO']);
        Country::create(['name' => 'Netherlands','acronym'=>'NED']);
        Country::create(['name' => 'New Zealand','acronym'=>'NZL']);
        Country::create(['name' => 'Qatar','acronym'=>'QAT']);
        Country::create(['name' => 'Saudi Arabia','acronym'=>'SAU']);
        Country::create(['name' => 'Singapore','acronym'=>'SGP']);
        Country::create(['name' => 'Spain','acronym'=>'ESP']);
        Country::create(['name' => 'Swiss','acronym'=>'CHE']);
        Country::create(['name' => 'Thailand','acronym'=>'THA']);
        Country::create(['name' => 'United Arab Emirates','acronym'=>'ARE']);
        Country::create(['name' => 'United Kingdom','acronym'=>'GBR']);
        Country::create(['name' => 'USA','acronym'=>'USA']);
    }
}
