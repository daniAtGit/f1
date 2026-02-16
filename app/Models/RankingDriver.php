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
        'edition_id',
        'team_id',
        'driver_id'
    ];

    public $timestamps = false;

    protected $casts = [
        //
    ];

    public function edition(){
        return $this->belongsTo(Edition::class);
    }

    public function team(){
        return $this->belongsTo(Team::class);
    }

    public function driver(){
        return $this->belongsTo(Driver::class);
    }
}
