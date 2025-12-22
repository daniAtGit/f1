<?php

namespace App\Models;

use Cohensive\OEmbed\Facades\OEmbed;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasUuids;

    protected $table="videos";

    protected $fillable = [
        'title',
        'url',
        'edition_circuit_id',
    ];

    protected $casts = [
        //
    ];

    public function editionCircuit(): BelongsTo
    {
        return $this->belongsTo(EditionCircuit::class);
    }

    public function getVideoTitle($value)
    {
        $embed = OEmbed::get($value);
        return $embed
            ? $embed->data()['title'] ?? ''
            : 'no titolo';
    }

    public function getVideo($value)
    {
        $embed = OEmbed::get($value);
        return $embed
            ? $embed->html(['width' => 350])
            : '';
    }
}
