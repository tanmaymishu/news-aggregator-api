<?php

namespace App\Services\News;

use Illuminate\Support\Collection;

abstract class NewsSource
{
    const NEWSAPI_ID = 'newsapi';

    const NYTIMES_ID = 'nytimes';

    const THEGUARDIAN_ID = 'theguardian';

    protected string $baseUrl;

    protected array $queryParams;

    protected array $searchResults;

    protected Collection $articles;

    public function __construct()
    {
        $this->articles = collect();
        $this->configure();
    }

    abstract protected function configure(): void;

    abstract public function mapArticle(array $article): array;

    abstract protected function getEndpoint(): string;

    use InteractsWithNewsSource;
}
