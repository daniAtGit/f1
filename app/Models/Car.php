<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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

    public function getImageUrl(): ?string
    {
        return Cache::remember("car-image:{$this->id}", now()->addWeek(), function () {
            $search = trim(implode(' ', array_filter([
                $this->team?->name,
                $this->name,
                'Formula One car',
            ])));

            if ($search === '') {
                return null;
            }

            try {
                $response = Http::timeout(8)
                    ->retry(2, 250)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->withUserAgent(env('WIKIMEDIA_USER_AGENT', 'F1ArchivioBot/1.0 (https://localhost; mailto:admin@example.com)'))
                    ->get('https://commons.wikimedia.org/w/api.php', [
                        'action' => 'query',
                        'format' => 'json',
                        'generator' => 'search',
                        'gsrsearch' => $search,
                        'gsrnamespace' => 6,
                        'gsrlimit' => 10,
                        'prop' => 'imageinfo',
                        'iiprop' => 'url|mime',
                    ]);

                if (! $response->ok()) {
                    return null;
                }

                return collect($response->json('query.pages', []))
                    ->filter(fn (array $page) => str_starts_with(data_get($page, 'imageinfo.0.mime', ''), 'image/'))
                    ->sortBy('index')
                    ->value('imageinfo.0.url');
            } catch (\Throwable $exception) {
                report($exception);

                return null;
            }
        });
    }
}
