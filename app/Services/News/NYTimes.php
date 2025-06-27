<?php

namespace App\Services\News;

class NYTimes extends NewsSource implements Sourcable
{
    public function __construct()
    {
        $nyTimesId = NewsSource::NYTIMES_ID;

        $this->baseUrl = config("services.news.{$nyTimesId}.base_url");
        $this->queryParams = [
            'page-size' => config("services.news.{$nyTimesId}.page_size"),
            'begin_date' => today()->subDay()->format('Ymd'),
            'end_date' => today()->format('Ymd'),
            'api-key' => config("services.news.{$nyTimesId}.key"),
        ];
    }

    public function search(?string $query): NewsSource
    {
        $path = '/svc/search/v2/articlesearch.json';

        $this->queryParams['q'] = $query ?? '';
        $firstResponse = $this->fetch($path);

        if (!$firstResponse->ok()) {
            throw new \RuntimeException("Failed to fetch articles from NewsApi {$firstResponse->body()}");
        }

        $totalPages = 100; // Enforced by NYTimes API
        $allResults = $firstResponse->json('response.docs');

        for($i = 2; $i <= $totalPages; $i++) {
            $this->queryParams['page'] = $i;

            $response = $this->fetch($path);
            if ($response->ok() && $results = $response->json('response.docs')) {
                foreach ($results as $result) {
                    $allResults[] = $result;
                }
            } else {
                break;
            }

        }

        $this->searchResults = $allResults;

        return $this;
    }

    public function mapArticle(array $article): array
    {
        return [
            'title' => $article['headline']['main'],
            'description' => $article['snippet'] ?? $article['headline']['main'],
            'content' => $article['abstract'] ?? $article['snippet'] ?? $article['headline']['main'],
            'category' => $article['category'] ?? 'Uncategorized',
            'source' => $article['source'] ?? 'The New York Times',
            'author' => $article['byline']['original'] ?? 'Staff Reporter',
            'web_url' => $article['web_url'] ?? '',
            'featured_image_url' => $article['multimedia']['default']['url'] ?? '',
            'published_at' => $article['pub_date'] ?? now()->toDateString(),
        ];
    }
}
