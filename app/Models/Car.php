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
        return Cache::remember("car-image:v9:{$this->id}", now()->addWeek(), function () {
            foreach ($this->wikipediaSearchTerms() as $search) {
                $imageUrl = $this->findImageOnWikipedia($search);

                if ($imageUrl) {
                    return $imageUrl;
                }
            }

            foreach ($this->imageCategoryNames() as $category) {
                $imageUrl = $this->findImageInWikimediaCategory($category);

                if ($imageUrl) {
                    return $imageUrl;
                }
            }

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

    private function wikipediaSearchTerms()
    {
        $team = trim((string) $this->team?->name);
        $teamWithoutEntryCode = $this->teamNameWithoutEntryCode($team);
        $name = trim((string) $this->name);
        $year = trim((string) $this->edition?->year);

        return collect([
            implode(' ', array_filter([$name, 'Formula One car'])),
            implode(' ', array_filter([$teamWithoutEntryCode, $name, 'Formula One car'])),
            implode(' ', array_filter([$name, $year, 'Formula One'])),
        ])->map(fn (string $search) => trim($search))
            ->filter()
            ->unique()
            ->values();
    }

    private function imageCategoryNames()
    {
        $team = trim((string) $this->team?->name);
        $name = trim((string) $this->name);
        $teamWithoutEntryCode = $this->teamNameWithoutEntryCode($team);
        $constructor = trim(preg_replace('/\b(scuderia|f1|formula\s*1|team|racing)\b/i', '', $team));

        return collect([
            implode(' ', array_filter([$team, $name])),
            implode(' ', array_filter([$teamWithoutEntryCode, $name])),
            implode(' ', array_filter([$constructor, $name])),
        ])->map(fn (string $category) => trim(preg_replace('/\s+/', ' ', $category)))
            ->filter()
            ->unique()
            ->values();
    }

    private function imageSearchTerms()
    {
        $team = trim((string) $this->team?->name);
        $teamWithoutEntryCode = $this->teamNameWithoutEntryCode($team);
        $name = trim((string) $this->name);
        $year = trim((string) $this->edition?->year);
        $teamNames = collect([$team, $teamWithoutEntryCode])->filter()->unique();

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

        return $teamNames
            ->flatMap(fn (string $teamName) => collect([
                implode(' ', array_filter([$teamName, $name, 'Formula One car', $year])),
                implode(' ', array_filter([$teamName, $name, 'F1 car', $year])),
                implode(' ', array_filter([$teamName, 'Formula One car', $year])),
            ])->merge($modelNames->flatMap(fn (string $model) => [
                implode(' ', array_filter([$teamName, $model, 'Formula One car', $year])),
                implode(' ', array_filter([$teamName, $model, 'F1 car', $year])),
            ])))
            ->map(fn (string $search) => trim($search))
            ->filter()
            ->unique()
            ->values();
    }

    private function teamNameWithoutEntryCode(string $team): string
    {
        return trim(preg_replace('/^(rb|rbr)\s+/i', '', $team));
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
                ->filter(fn (array $page) => $this->isFormulaOneImageTitle(data_get($page, 'title', '')))
                ->sortBy('index')
                ->value('imageinfo.0.url');
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function findImageOnWikipedia(string $search): ?string
    {
        try {
            $response = Http::timeout(8)
                ->retry(2, 250)
                ->withHeaders(['Accept' => 'application/json'])
                ->withUserAgent(env('WIKIMEDIA_USER_AGENT', 'F1ArchivioBot/1.0 (https://localhost; mailto:admin@example.com)'))
                ->get('https://en.wikipedia.org/w/api.php', [
                    'action' => 'query',
                    'format' => 'json',
                    'generator' => 'search',
                    'gsrsearch' => $search,
                    'gsrnamespace' => 0,
                    'gsrlimit' => 3,
                    'prop' => 'pageimages',
                    'piprop' => 'original|thumbnail',
                    'pithumbsize' => 900,
                ]);

            if (! $response->ok()) {
                return null;
            }

            return collect($response->json('query.pages', []))
                ->sortBy('index')
                ->filter(fn (array $page) => $this->titleMatchesCarName(data_get($page, 'title', '')))
                ->map(fn (array $page) => data_get($page, 'original.source') ?? data_get($page, 'thumbnail.source'))
                ->filter()
                ->first();
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function titleMatchesCarName(string $title): bool
    {
        $normalizedCarName = preg_replace('/[^a-z0-9]+/i', '', (string) $this->name);
        $normalizedTitle = preg_replace('/[^a-z0-9]+/i', '', $title);

        return $normalizedCarName !== ''
            && str_contains(strtolower($normalizedTitle), strtolower($normalizedCarName));
    }

    private function findImageInWikimediaCategory(string $category): ?string
    {
        try {
            $response = Http::timeout(8)
                ->retry(2, 250)
                ->withHeaders(['Accept' => 'application/json'])
                ->withUserAgent(env('WIKIMEDIA_USER_AGENT', 'F1ArchivioBot/1.0 (https://localhost; mailto:admin@example.com)'))
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action' => 'query',
                    'format' => 'json',
                    'generator' => 'categorymembers',
                    'gcmtitle' => 'Category:'.$category,
                    'gcmtype' => 'file',
                    'gcmlimit' => 10,
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

    private function isFormulaOneImageTitle(string $title): bool
    {
        $normalizedTitle = ' '.preg_replace('/[^a-z0-9]+/i', ' ', strtolower($title)).' ';
        $hasRoadCarTerm = collect([
            ' stradale ',
            ' road car ',
            ' interior ',
            ' dashboard ',
            ' cockpit interior ',
            ' production car ',
            ' street car ',
        ])->contains(fn (string $term) => str_contains($normalizedTitle, $term));

        // La query include sempre "Formula One car" e il nome del team: molti
        // file corretti hanno però nomi come "Ferrari SF-24" senza "F1" nel titolo.
        // Escludere i soli segnali delle auto stradali evita di perdere quelle foto.
        return ! $hasRoadCarTerm;
    }
}
