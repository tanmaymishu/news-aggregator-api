<?php

namespace App\Services\News;

use Illuminate\Support\Carbon;

class NewsApi extends NewsSource implements Sourcable
{
    public function __construct()
    {
        $newsApiId = NewsSource::NEWSAPI_ID;

        $this->baseUrl = config("services.news.{$newsApiId}.base_url");
        $this->queryParams = [
            'language' => 'en',
            'apiKey' => config("services.news.{$newsApiId}.key"),
            'pageSize' => config("services.news.{$newsApiId}.page_size"),
        ];
    }

    public function search(?string $query): NewsSource
    {
        $this->queryParams['q'] = $query ?? 'headline';

        $firstResponse = $this->fetch('/everything');
        if (! $firstResponse->ok()) {
            throw new \RuntimeException("Failed to fetch articles from NewsApi {$firstResponse->body()}");
        }
        $allResults = $firstResponse->json('articles');

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

        $this->searchResults = $allResults;

        return $this;
    }

    public function mapArticle(array $article): array
    {
        return [
            'title' => $article['title'],
            'description' => $article['description'] ?? $article['title'],
            'content' => $article['content'] ?? $article['description'] ?? $article['title'],
            'category' => $article['category'] ?? 'Uncategorized',
            'source' => $article['source']['name'] ?? 'NewsApi',
            'author' => $article['author'] ?? 'Staff Reporter',
            'web_url' => $article['url'] ?? '',
            'featured_image_url' => $article['urlToImage'] ?? '',
            'published_at' => date('Y-m-d H:i:s', strtotime($article['publishedAt'] ?? now()->toDateString())),
        ];
    }
}
