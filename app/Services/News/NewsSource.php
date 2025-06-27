<?php

namespace App\Services\News;

abstract class NewsSource
{
    const NEWSAPI_ID = "newsapi";
    const NYTIMES_ID = "nytimes";
    const THEGUARDIAN_ID = "theguardian";

    protected string $baseUrl;
    protected array $queryParams;
    protected array $searchResults;
    protected array $articles;

    use InteractsWithNewsSource;
}
