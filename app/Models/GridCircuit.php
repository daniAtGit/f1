<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GridCircuit extends Model
{
    use HasUuids;

    protected $table="grid_circuit";

    protected $fillable = [
        'position',
        'driver_team_id',
        'time'
    ];

    protected $casts = [
        //
    ];

    public function driverTeam()
    {
        return $this->belongsTo(DriverTeam::class);
    }
}
