<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RankingTeam extends Model
{
    use HasUuids;

    protected $table="ranking_team";

    protected $fillable = [
        'points',
        'team_id',
        'edition_id'
    ];

    protected $casts = [
        //
    ];
}
