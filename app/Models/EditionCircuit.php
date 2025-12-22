<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EditionCircuit extends Model
{
    use HasUuids;

    protected $table="edition_circuit";

    protected $fillable = [
        'edition_id',
        'circuit_id',
        'round',
        'date'
    ];

    public $timestamps = false;

    protected $casts = [
        'date' => 'date'
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }

    public function circuit()
    {
        return $this->belongsTo(Circuit::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'edition_circuit_id','id');
    }

    public function sprint()
    {
        return $this->hasMany(SprintCircuit::class, 'edition_circuit_id','id');
    }

    public function grid()
    {
        return $this->hasMany(GridCircuit::class, 'edition_circuit_id','id');
    }

    public function race()
    {
        return $this->hasMany(RaceCircuit::class, 'edition_circuit_id','id');
    }
}
