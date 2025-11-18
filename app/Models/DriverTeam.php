<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DriverTeam extends Model
{
    use HasUuids;

    protected $table="driver_team";

    protected $fillable = [
        'edition_id',
        'driver_id',
        'team_id'
    ];

    public $timestamps = false;

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id','id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id','id');
    }
}
