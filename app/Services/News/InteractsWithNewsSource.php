<?php

declare(strict_types=1);

namespace App\Services\News;

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

    public function normalize(): Collection
    {
        $this->assertSearchResults();

        return collect($this->searchResults)
            ->map(fn ($article) => $this->mapArticle($article))
            ->filter() // Remove empty arrays
            ->values();
    }

    public function fetch(string $path): Response
    {
        $this->assertQueryParams();

        $http = app()->environment('testing')
        ? Http::timeout(30)
        : Http::retry(3, 3000, throw: false)->timeout(30);

        $response = $http->get($this->baseUrl.$path, $this->queryParams);

        if (! $response->successful()) {
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

    private function assertQueryParams()
    {
        if (empty($this->queryParams)) {
            throw new NewsSourceException('Query parameters not configured');
        }
    }

    protected function parseDate(?string $date): string
    {
        if (! $date) {
            return now()->format('Y-m-d H:i:s');
        }

        $timestamp = strtotime($date);

        return $timestamp !== false
            ? date('Y-m-d H:i:s', $timestamp)
            : now()->format('Y-m-d H:i:s');
    }
}
