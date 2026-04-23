<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasUuids;

    protected $table="countries";

    protected $fillable = [
        'name',
        'acronym'
    ];

    protected $casts = [
        //
    ];

    public function getFlagIconUrlAttribute(): ?string
    {
        $iso2ByAcronym = [
            'ARG' => 'ar',
            'ARE' => 'ae',
            'AUS' => 'au',
            'AUT' => 'at',
            'AZE' => 'az',
            'BEL' => 'be',
            'BHR' => 'bh',
            'BRA' => 'br',
            'CAN' => 'ca',
            'CHE' => 'ch',
            'CHN' => 'cn',
            'DEU' => 'de',
            'ESP' => 'es',
            'FRA' => 'fr',
            'GBR' => 'gb',
            'HUN' => 'hu',
            'ITA' => 'it',
            'JPN' => 'jp',
            'MCO' => 'mc',
            'MEX' => 'mx',
            'NED' => 'nl',
            'NZL' => 'nz',
            'QAT' => 'qa',
            'SAU' => 'sa',
            'SGP' => 'sg',
            'THA' => 'th',
            'USA' => 'us',
        ];

        $iso2 = $iso2ByAcronym[strtoupper((string) $this->acronym)] ?? null;
        if (!$iso2) { return null; }

        return "https://flagcdn.com/w40/{$iso2}.png";
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }
}
