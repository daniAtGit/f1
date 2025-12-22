<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SprintCircuit extends Model
{
    use HasUuids;

    protected $table="sprint_circuit";

    protected $fillable = [
        'position',
        'driver_team_id',
        'circuit_id',
        'edition_circuit_id',
        'time'
    ];

    public $timestamps = false;

    protected $casts = [
        //
    ];

    public function driverTeam()
    {
        return $this->belongsTo(DriverTeam::class);
    }
}
