<?php

namespace App\Services\News;

abstract class NewsSource
{
    const NEWSAPI_ID = 'newsapi';

    const NYTIMES_ID = 'nytimes';

    const THEGUARDIAN_ID = 'theguardian';

    protected string $baseUrl;

    protected array $queryParams;

    protected array $searchResults;

    public function __construct()
    {
        $this->configure();
    }

    abstract protected function configure(): void;

    abstract public function mapArticle(array $article): array;

    abstract protected function getEndpoint(): string;

    use InteractsWithNewsSource;
}
