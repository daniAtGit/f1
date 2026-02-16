<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
    use HasUuids;

    protected $table="editions";

    protected $fillable = [
        'edition',
        'year',
        'wikipedia'
    ];

    protected $casts = [
        //
    ];

    public function driversTeams()
    {
        return $this->hasMany(DriverTeam::class, 'edition_id','id');
    }

    public function circuits()
    {
        return $this->hasMany(EditionCircuit::class, 'edition_id','id');
    }

    public function driverTeams()
    {
        return $this->hasMany(DriverTeam::class, 'driver_team_id','id');
    }

    public function rankingTeams()
    {
        return $this->hasMany(RankingTeam::class, 'edition_id','id');
    }

    public function rankingDrivers()
    {
        return $this->hasMany(RankingDriver::class, 'edition_id','id');
    }
}
