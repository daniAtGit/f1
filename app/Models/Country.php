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
    ];

    protected $casts = [
        //
    ];

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
