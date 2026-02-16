<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    use HasUuids;

    protected $table="cars";

    protected $fillable = [
        'name',
        'team_id',
        'edition_id',
    ];

    protected $casts = [];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}
