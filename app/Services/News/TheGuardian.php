<?php

namespace App\Services\News;

use App\Exceptions\NewsSourceException;

class TheGuardian extends NewsSource implements Sourcable
{
    public function search(?string $query): NewsSource
    {
        $this->queryParams['q'] = $query ?? '';
        $firstResponse = $this->fetch('/search');
        if (! $firstResponse->ok()) {
            throw new NewsSourceException("Failed to fetch articles from TheGuardian {$firstResponse->body()}");
        }

        $totalPages = $firstResponse->json('response.pages') ?? 0;
        $allResults = $firstResponse->json('response.results');

        for ($i = 2; $i <= $totalPages; $i++) {
            $this->queryParams['page'] = $i;
            $response = $this->fetch($this->getEndpoint());

            if ($results = $response->json('response.results')) {
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
        // If some article doesn't even have a title or publish date field,
        // do not add that article.
        if ((empty($article['webTitle']) && empty($article['fields']['headline'])) || empty($article['webPublicationDate'])) {
            return [];
        }

        return [
            'title' => $article['webTitle'] ?? $article['fields']['headline'],
            'description' => $article['webTitle'] ?? $article['fields']['headline'],
            'content' => $article['fields']['body'] ?? $article['fields']['headline'],
            'category' => $article['pillarName'] ?? 'Uncategorized',
            'source' => 'The Guardian',
            'author' => $article['fields']['byline'] ?? 'Staff Reporter',
            'web_url' => $article['webUrl'] ?? '',
            'featured_image_url' => $article['fields']['thumbnail'] ?? '',
            'published_at' => $this->parseDate($article['webPublicationDate']),
        ];
    }

    protected function configure(): void
    {
        $theGuardianId = NewsSource::THEGUARDIAN_ID;

        $this->baseUrl = config("services.news.{$theGuardianId}.base_url");
        $this->queryParams = [
            'page-size' => config("services.news.{$theGuardianId}.page_size"),
            'from-date' => today()->subDay()->format('Y-m-d'),
            'to-date' => today()->format('Y-m-d'),
            'api-key' => config("services.news.{$theGuardianId}.key"),
            'show-fields' => 'thumbnail,headline,byline',
        ];
    }

    protected function getEndpoint(): string
    {
        return '/search';
    }
}
