<?php

namespace App\Services\News;

use App\Actions\SyncArticlesAction;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait InteractsWithNewsSource
{
    public function normalize(): NewsSource
    {
        $this->articles = collect($this->searchResults)
            ->map(fn($article) => $this->mapArticle($article))
            ->toArray();

        return $this;
    }

    public function save(): NewsSource
    {
        (new SyncArticlesAction())->handle($this->articles);

        return $this;
    }

    public function fetch(string $path): Response
    {
        return Http::retry(3, 3000, throw: false)->get($this->baseUrl . $path, $this->queryParams);
    }

}
