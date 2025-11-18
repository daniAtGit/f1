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
        Country::create(['name' => 'Argentina']);
        Country::create(['name' => 'Australia']);
        Country::create(['name' => 'Austria']);
        Country::create(['name' => 'Azerbaijan']);
        Country::create(['name' => 'Barhain']);
        Country::create(['name' => 'Belgium']);
        Country::create(['name' => 'Brazil']);
        Country::create(['name' => 'Canada']);
        Country::create(['name' => 'China']);
        Country::create(['name' => 'Hungary']);
        Country::create(['name' => 'France']);
        Country::create(['name' => 'Germany']);
        Country::create(['name' => 'Italy']);
        Country::create(['name' => 'Japan']);
        Country::create(['name' => 'Mexico']);
        Country::create(['name' => 'Monaco']);
        Country::create(['name' => 'Netherlands']);
        Country::create(['name' => 'New Zealand']);
        Country::create(['name' => 'Qatar']);
        Country::create(['name' => 'Saudi Arabia']);
        Country::create(['name' => 'Singapore']);
        Country::create(['name' => 'Spain']);
        Country::create(['name' => 'Swiss']);
        Country::create(['name' => 'Thailand']);
        Country::create(['name' => 'United Arab Emirates']);
        Country::create(['name' => 'United Kingdom']);
        Country::create(['name' => 'USA']);
    }
}
