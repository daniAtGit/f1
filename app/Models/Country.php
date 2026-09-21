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
            'COL' => 'co',
            'CZE' => 'cz',
            'DEN' => 'dk',
            'DEU' => 'de',
            'ESP' => 'es',
            'FIN' => 'fi',
            'FRA' => 'fr',
            'GBR' => 'gb',
            'HUN' => 'hu',
            'IDN' => 'id',
            'IND' => 'in',
            'IRL' => 'ie',
            'ITA' => 'it',
            'JPN' => 'jp',
            'KOR' => 'kr',
            'MCO' => 'mc',
            'MEX' => 'mx',
            'MYS' => 'my',
            'NED' => 'nl',
            'NZL' => 'nz',
            'POL' => 'pl',
            'PRT' => 'pt',
            'QAT' => 'qa',
            'RUS' => 'ru',
            'SAU' => 'sa',
            'SGP' => 'sg',
            'SWE' => 'se',
            'THA' => 'th',
            'TUR' => 'tr',
            'USA' => 'us',
            'VEN' => 've',
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
