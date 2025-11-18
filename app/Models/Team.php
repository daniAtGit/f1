<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use HasUuids;

    protected $table="teams";

    protected $fillable = [
        'name',
        'color',
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
}
