<?php

namespace App\Services\News;

use App\Actions\SyncArticlesAction;
use App\Exceptions\NewsSourceException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

trait InteractsWithNewsSource
{
    public function getSearchResults(): array
    {
        return $this->searchResults;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function normalize(): NewsSource
    {
        $this->assertSearchResults();

        $this->articles = collect($this->searchResults)
            ->map(fn ($article) => $this->mapArticle($article))
            ->filter()
            ->values();

        return $this;
    }

    public function save(): NewsSource
    {
        $this->assertArticles();

        (new SyncArticlesAction)->handle($this->articles->toArray());

        return $this;
    }

    public function fetch(string $path): Response
    {
        $this->assertQueryParams();

        $http = app()->environment('testing')
        ? Http::timeout(30)
        : Http::retry(3, 3000, throw: false)->timeout(30);

        $response = $http->get($this->baseUrl.$path, $this->queryParams);

        if (!$response->successful()) {
            logger()->error("Failed to fetch from {$this->baseUrl}{$path}", [
                'status' => $response->status(),
                'body' => $response->body(),
                'params' => $this->queryParams,
            ]);
        }

        return $response;
    }

    private function assertSearchResults()
    {
        if (empty($this->searchResults)) {
            throw new NewsSourceException('No search results found');
        }
    }

    private function assertArticles()
    {
        if ($this->articles->isEmpty()) {
            throw new NewsSourceException('No articles to save');
        }
    }

    private function assertQueryParams()
    {
        if (empty($this->queryParams)) {
            throw new NewsSourceException('Query parameters not configured');
        }
    }

    protected function parseDate(?string $date): string
    {
        try {
            return $date ? date('Y-m-d H:i:s', strtotime($date)) : now()->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now()->format('Y-m-d H:i:s');
        }
    }
}
