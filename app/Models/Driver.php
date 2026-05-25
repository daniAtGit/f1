<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use HasUuids;

    protected $table="drivers";

    protected $fillable = [
        'name',
        'birth_year',
        'country_id',
        'wikipedia'
    ];

    protected $casts = [
        //
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function driverTeams()
    {
        return $this->hasMany(DriverTeam::class);
    }

    public function gridCircuits()
    {
        return $this->hasManyThrough(
            GridCircuit::class,
            DriverTeam::class,
            'driver_id',
            'driver_team_id'
        );
    }

    public function RaceCircuits()
    {
        return $this->hasManyThrough(
            RaceCircuit::class,
            DriverTeam::class,
            'driver_id',
            'driver_team_id'
        );
    }

    public function SprintCircuits()
    {
        return $this->hasManyThrough(
            SprintCircuit::class,
            DriverTeam::class,
            'driver_id',
            'driver_team_id'
        );
    }
}
