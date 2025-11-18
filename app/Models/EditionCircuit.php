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

    public function circuit()
    {
        return $this->belongsTo(Circuit::class);
    }

    public function grid()
    {
        return $this->hasMany(GridCircuit::class, 'edition_circuit_id','id');
    }

    protected function grid3(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->grid()->get()->sortBy('position')->take(3),
        );
    }
}
