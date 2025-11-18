<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RankingDriver extends Model
{
    use HasUuids;

    protected $table="ranking_driver";

    protected $fillable = [
        'points',
        'driver_team_id'
    ];

    protected $casts = [
        //
    ];
}
