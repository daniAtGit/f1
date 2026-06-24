<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Circuit extends Model
{
    use HasUuids;

    protected $table="circuits";

    protected $fillable = [
        'name',
        'city',
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

    public function getImgCircuitFromGoogle($cosa = null, $anno = null): ?string
    {
        return $this->getImgCircuitFromWikimedia($cosa, $anno);
    }

    public function getImgCircuitFromWiki($cosa = null, $anno = null): ?string
    {
        return $this->getImgCircuitFromWikimedia($cosa, $anno);
    }

    private function getImgCircuitFromWikimedia($cosa = null, $anno = null): ?string
    {
        $fromTitle = $this->getImageFromWikipediaTitle();
        if (!empty($fromTitle)) {
            return $fromTitle;
        }

        $terms = array_filter([
            trim($this->name.' '.($cosa ?? '').' '.($anno ?? '')),
            $this->name.' formula one circuit',
            $this->name.' race track',
            trim($this->name.' '.$this->city),
            $this->name,
        ]);

        foreach ($terms as $term) {
            $url = $this->getImageFromWikipediaSearch($term);
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

            $pages = collect($response->json('query.pages', []));
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
