<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ResultCircuit extends Model
{
    use HasUuids;

    protected $table="result_circuit";

    protected $fillable = [
        'position',
        'driver_team_id',
        'circuit_id',
        'fast_lap'
    ];

    protected $casts = [
        //
    ];
}
