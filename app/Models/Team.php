<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Client\PendingRequest;
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
        $fromTitle = $this->getImageFromWikipediaTitle();
        if (!empty($fromTitle)) {
            return $fromTitle;
        }

        $teamName = $this->normalizedTeamName();
        $terms = array_unique(array_filter([
            $teamName.' formula one team',
            $teamName.' f1 team',
            trim($teamName.' '.($cosa ?? '').' '.($anno ?? '')),
            $teamName,
        ]));

        foreach ($terms as $term) {
            $url = $this->getImageFromWikipediaSearch($term);
            if (!empty($url)) {
                return $url;
            }
        }

        foreach ($terms as $term) {
            $url = $this->getImageFromWikimediaCommonsSearch($term);
            if (!empty($url)) {
                return $url;
            }
        }

        return null;
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
                ->filter(fn ($page) => $this->titleMatchesTeamName(data_get($page, 'title')))
                ->filter(fn ($page) => str_starts_with(data_get($page, 'imageinfo.0.mime', ''), 'image/'))
                ->sortBy([
                    fn ($page) => $this->teamTitlePriority(data_get($page, 'title')),
                    fn ($page) => data_get($page, 'index', PHP_INT_MAX),
                ]);

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
        return trim(preg_replace('/\s+/', ' ', mb_strtolower($value)));
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
