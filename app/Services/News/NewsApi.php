<?php

declare(strict_types=1);

namespace App\Services\News;

use App\Exceptions\NewsSourceException;

final class NewsApi extends NewsSource implements Sourcable
{
    public function search(?string $query): NewsSource
    {
        $this->queryParams['q'] = $query ?? 'headline';

        $firstResponse = $this->fetch($this->getEndpoint());

        if (! $firstResponse->successful()) {
            throw new NewsSourceException("Failed to fetch articles from NewsApi {$firstResponse->body()}");
        }

        $this->searchResults = $firstResponse->json('articles', []);

        // TODO: NewsAPI v2 is limited to 100 results for a "developer" account.
        // So commenting the pagination.

        //        $pageSize = $this->queryParams['pageSize'];
        //        $totalResults = $firstResponse->json('totalResults') ?? 0;
        //        $totalPages = $totalResults / $pageSize;

        //        for($i = 2; $i <= $totalPages; $i++) {
        //            $this->queryParams['page'] = $i;
        //            $response = $this->fetch('/everything');
        //
        //            if ($results = $response->json('articles')) {
        //                foreach ($results as $result) {
        //                    $allResults[] = $result;
        //                }
        //            }
        //        }

        return $this;
    }

    public function mapArticle(array $article): array
    {
        // If some article doesn't even have a title or publish date field,
        // do not add that article.
        if (empty($article['title']) || empty($article['publishedAt'])) {
            return [];
        }

        return [
            'title' => $article['title'],
            'description' => $article['description'] ?? $article['title'],
            'content' => $article['content'] ?? $article['description'] ?? $article['title'],
            'category' => $article['category'] ?? 'Uncategorized',
            'source' => $article['source']['name'] ?? 'NewsApi',
            'author' => $article['author'] ?? 'Staff Reporter',
            'web_url' => $article['url'] ?? '',
            'featured_image_url' => $article['urlToImage'] ?? '',
            'published_at' => $this->parseDate($article['publishedAt']),
        ];
    }

    protected function configure(): void
    {
        $newsApiId = parent::NEWSAPI_ID;

        $this->baseUrl = config("services.news.{$newsApiId}.base_url");
        $this->queryParams = [
            'language' => 'en',
            'apiKey' => config("services.news.{$newsApiId}.key"),
            'pageSize' => config("services.news.{$newsApiId}.page_size"),
        ];
    }

    protected function getEndpoint(): string
    {
        return '/everything';
    }
}
