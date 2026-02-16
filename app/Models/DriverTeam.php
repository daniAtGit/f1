<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DriverTeam extends Model
{
    use HasUuids;

    protected $table = "driver_team";

    protected $fillable = [
        'edition_id',
        'driver_id',
        'team_id',
        'car_id',
        'number'
    ];

    public $timestamps = false;

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    public function gridCircuits()
    {
        return $this->hasMany(GridCircuit::class);
    }

    public function sprintCircuits()
    {
        return $this->hasMany(SprintCircuit::class);
    }

    public function raceCircuits()
    {
        return $this->hasMany(RaceCircuit::class);
    }

    public function rankingDrivers()
    {
        return $this->hasMany(RankingDriver::class, 'edition_id', 'id');
    }
}
