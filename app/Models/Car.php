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
        return Cache::remember("car-image:v2:{$this->id}", now()->addWeek(), function () {
            $searches = $this->imageSearchTerms();

            if ($searches->isEmpty()) {
                return null;
            }

            foreach ($searches as $search) {
                $imageUrl = $this->findImageOnWikimedia($search);

                if ($imageUrl) {
                    return $imageUrl;
                }
            }

            return null;
        });
    }

    private function imageSearchTerms()
    {
        $team = trim((string) $this->team?->name);
        $name = trim((string) $this->name);
        $year = trim((string) $this->edition?->year);

        $modelNames = collect(preg_split('/[\s,\/]+/', $name) ?: [])
            ->map(fn (string $part) => trim($part, " \t\n\r\0\x0B()[]"))
            ->filter(fn (string $part) => strlen($part) >= 3 && preg_match('/\d/', $part))
            ->flatMap(function (string $model) {
                $fallbacks = [$model];
                $withoutTrailingVariant = preg_replace('/(?<=\d)[a-z]$/i', '', $model);
                $withoutSuffix = preg_replace('/-[a-z]+$/i', '', $model);

                if ($withoutTrailingVariant !== $model) {
                    $fallbacks[] = $withoutTrailingVariant;
                }

                if ($withoutSuffix !== $model) {
                    $fallbacks[] = $withoutSuffix;
                }

                return $fallbacks;
            })
            ->filter()
            ->unique()
            ->values();

        return collect([
            implode(' ', array_filter([$team, $name, $year])),
            implode(' ', array_filter([$team, $name])),
            $name,
        ])
            ->merge($modelNames->flatMap(fn (string $model) => [
                implode(' ', array_filter([$team, $model, $year])),
                implode(' ', array_filter([$team, $model])),
                $model,
            ]))
            ->map(fn (string $search) => trim($search))
            ->filter()
            ->unique()
            ->values();
    }

    private function findImageOnWikimedia(string $search): ?string
    {
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
    }
}
