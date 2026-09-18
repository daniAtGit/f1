<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'team_id', 'id');
    }

    public function driverTeams(): HasMany
    {
        return $this->hasMany(DriverTeam::class, 'team_id', 'id');
    }

    public function gridCircuits(): HasManyThrough
    {
        return $this->hasManyThrough(GridCircuit::class, DriverTeam::class, 'team_id', 'driver_team_id');
    }

    public function raceCircuits(): HasManyThrough
    {
        return $this->hasManyThrough(RaceCircuit::class, DriverTeam::class, 'team_id', 'driver_team_id');
    }

    public function sprintCircuits(): HasManyThrough
    {
        return $this->hasManyThrough(SprintCircuit::class, DriverTeam::class, 'team_id', 'driver_team_id');
    }

    public function getImgTeamFromGoogle($cosa = null, $anno = null): ?string
    {
        return $this->getImgTeamFromWikimedia($cosa, $anno);
    }

    public function getImgTeamFromWiki($cosa = null, $anno = null): ?string
    {
        return $this->getImgTeamFromWikimedia($cosa, $anno);
    }

    private function getImgTeamFromWikimedia($cosa = null, $anno = null): ?string
    {
        return Cache::remember("team-image:v3:{$this->id}", now()->addWeek(), function () {
            $logoUrl = $this->getLogoFromWikidata();
            if (!empty($logoUrl)) {
                return $logoUrl;
            }

            $teamName = $this->normalizedTeamName();
            $terms = array_unique(array_filter([
                $teamName.' formula one team logo',
                $teamName.' racing team logo',
                $teamName.' logo',
                $teamName.' emblem',
                $teamName.' wordmark',
            ]));

            foreach ($terms as $term) {
                $url = $this->getImageFromWikimediaCommonsSearch($term);
                if (!empty($url)) {
                    return $url;
                }
            }

            return null;
        });
    }

    private function getLogoFromWikidata(): ?string
    {
        $title = $this->extractWikipediaTitleFromUrl();
        if (empty($title)) {
            return null;
        }

        try {
            $pageResponse = $this->wikiHttp()->get($this->wikipediaApiUrl(), [
                'action' => 'query',
                'format' => 'json',
                'redirects' => 1,
                'prop' => 'pageprops',
                'titles' => $title,
            ]);

            if (!$pageResponse->ok()) {
                return null;
            }

            $wikidataId = collect($pageResponse->json('query.pages', []))
                ->pluck('pageprops.wikibase_item')
                ->filter()
                ->first();

            if (empty($wikidataId)) {
                return null;
            }

            $claimsResponse = $this->wikiHttp()->get('https://www.wikidata.org/w/api.php', [
                'action' => 'wbgetclaims',
                'format' => 'json',
                'entity' => $wikidataId,
                'property' => 'P154',
            ]);

            if (!$claimsResponse->ok()) {
                return null;
            }

            $filename = data_get($claimsResponse->json(), 'claims.P154.0.mainsnak.datavalue.value');

            return is_string($filename) && $filename !== ''
                ? $this->getWikimediaFileUrl($filename)
                : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function getWikimediaFileUrl(string $filename): ?string
    {
        try {
            $response = $this->wikiHttp()->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'titles' => 'File:'.$filename,
                'prop' => 'imageinfo',
                'iiprop' => 'url|mime',
            ]);

            if (!$response->ok()) {
                return null;
            }

            return collect($response->json('query.pages', []))
                ->filter(fn ($page) => str_starts_with(data_get($page, 'imageinfo.0.mime', ''), 'image/'))
                ->pluck('imageinfo.0.url')
                ->filter()
                ->first();
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function getImageFromWikipediaTitle(): ?string
    {
        $title = $this->extractWikipediaTitleFromUrl();
        if (empty($title)) {
            return null;
        }

        if (!$this->titleMatchesTeamName($title) || $this->teamTitlePriority($title) > 0) {
            return null;
        }

        try {
            $response = $this->wikiHttp()->get('https://en.wikipedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'redirects' => 1,
                'prop' => 'pageimages',
                'piprop' => 'original|thumbnail',
                'pithumbsize' => 600,
                'titles' => $title,
            ]);

            if (!$response->ok()) {
                return null;
            }

            $pages = $response->json('query.pages', []);
            foreach ($pages as $page) {
                $url = $page['original']['source'] ?? $page['thumbnail']['source'] ?? null;
                if (!empty($url)) {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    private function getImageFromWikipediaSearch(string $search): ?string
    {
        try {
            $response = $this->wikiHttp()->get('https://en.wikipedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'generator' => 'search',
                'gsrsearch' => $search,
                'gsrnamespace' => 0,
                'gsrlimit' => 5,
                'prop' => 'pageimages',
                'piprop' => 'original|thumbnail',
                'pithumbsize' => 600,
            ]);

            if (!$response->ok()) {
                return null;
            }

            $pages = collect($response->json('query.pages', []))
                ->filter(fn ($page) => $this->titleMatchesTeamName(data_get($page, 'title')))
                ->filter(fn ($page) => $this->teamTitlePriority(data_get($page, 'title')) === 0)
                ->sortBy('index');

            foreach ($pages as $page) {
                $url = data_get($page, 'original.source') ?? data_get($page, 'thumbnail.source');
                if (!empty($url)) {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    private function getImageFromWikimediaCommonsSearch(string $search): ?string
    {
        try {
            $response = $this->wikiHttp()->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'generator' => 'search',
                'gsrsearch' => $search,
                'gsrnamespace' => 6,
                'gsrlimit' => 10,
                'prop' => 'imageinfo',
                'iiprop' => 'url|mime',
            ]);

            if (!$response->ok()) {
                return null;
            }

            $pages = collect($response->json('query.pages', []))
                ->filter(fn ($page) => $this->isTeamIdentityImageTitle(data_get($page, 'title')))
                ->filter(fn ($page) => str_starts_with(data_get($page, 'imageinfo.0.mime', ''), 'image/'))
                ->sortBy(fn ($page) => data_get($page, 'index', PHP_INT_MAX));

            foreach ($pages as $page) {
                $url = data_get($page, 'imageinfo.0.url');
                if (!empty($url)) {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    private function isTeamIdentityImageTitle(?string $title): bool
    {
        if (empty($title)) {
            return false;
        }

        $normalizedTitle = $this->normalizeTeamTitle($title);
        $hasIdentityKeyword = collect(['logo', 'emblem', 'badge', 'wordmark'])
            ->contains(fn (string $keyword) => str_contains($normalizedTitle, $keyword));

        if (!$hasIdentityKeyword) {
            return false;
        }

        $describesVehicleOrPerson = collect([
            ' car ',
            ' automobile ',
            ' truck ',
            ' vehicle ',
            ' driver ',
            ' portrait ',
            ' helmet ',
            ' wheel ',
            ' on the side ',
            ' wearing ',
        ])->contains(fn (string $keyword) => str_contains(' '.$normalizedTitle.' ', $keyword));

        if ($describesVehicleOrPerson) {
            return false;
        }

        $identityWords = collect(preg_split('/\s+/', $this->normalizedTeamName()) ?: [])
            ->reject(fn (string $word) => in_array($word, ['f1', 'formula', 'one', 'team', 'racing', 'scuderia', 'grand', 'prix'], true))
            ->filter(fn (string $word) => mb_strlen($word) >= 3);

        return $identityWords->isNotEmpty()
            && $identityWords->contains(fn (string $word) => str_contains($normalizedTitle, $word));
    }

    private function titleMatchesTeamName(?string $title): bool
    {
        if (empty($title) || empty($this->name)) {
            return false;
        }

        $teamName = $this->normalizedTeamName();
        if (empty($teamName)) {
            return false;
        }

        return str_contains($this->normalizeTeamTitle($title), $teamName);
    }

    private function teamTitlePriority(?string $title): int
    {
        $normalizedTitle = $this->normalizeTeamTitle($title ?? '');

        if ($normalizedTitle === $this->normalizedTeamName()) {
            return 0;
        }

        foreach (['formula one', 'team', 'racing', 'scuderia', 'constructor', 'grand prix'] as $teamKeyword) {
            if (str_contains($normalizedTitle, $teamKeyword)) {
                return 0;
            }
        }

        return 1;
    }

    private function normalizedTeamName(): string
    {
        $name = $this->normalizeTeamTitle($this->name);

        return trim(preg_replace('/\b(f1|formula one|formula 1|team)\b/i', '', $name));
    }

    private function normalizeTeamTitle(string $value): string
    {
        $value = preg_replace('/[^\pL\pN]+/u', ' ', mb_strtolower($value));

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private function wikipediaApiUrl(): string
    {
        $host = parse_url((string) $this->wikipedia, PHP_URL_HOST);

        if (!is_string($host) || !str_ends_with($host, '.wikipedia.org')) {
            $host = 'en.wikipedia.org';
        }

        return 'https://'.$host.'/w/api.php';
    }

    private function extractWikipediaTitleFromUrl(): ?string
    {
        if (empty($this->wikipedia)) {
            return null;
        }

        $path = parse_url($this->wikipedia, PHP_URL_PATH);
        if (empty($path) || !str_contains($path, '/wiki/')) {
            return null;
        }

        $title = substr($path, strpos($path, '/wiki/') + 6);
        $title = urldecode(str_replace('_', ' ', $title));

        return $title ?: null;
    }

    private function wikiHttp(): PendingRequest
    {
        $userAgent = env('WIKIMEDIA_USER_AGENT', 'F1ArchivioBot/1.0 (https://localhost; mailto:admin@example.com)');

        return Http::timeout(8)
            ->retry(2, 250)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->withUserAgent($userAgent);
    }
}
