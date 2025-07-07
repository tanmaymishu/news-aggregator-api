<?php

declare(strict_types=1);

namespace App\Services\News;

use App\Actions\SyncArticlesAction;
use App\Exceptions\NewsSourceException;
use Illuminate\Support\Collection;

final class Aggregator
{
    private Sourcable $source;

    private Collection $articles;

    public function __construct()
    {
        $this->articles = collect();
    }

    public function setSource(Sourcable $source): Aggregator
    {
        $this->source = $source;

        return $this;
    }

    public function aggregate(?string $keyword): Aggregator
    {
        $this->articles = $this->source->search($keyword)
            ->normalize();

        return $this;
    }

    public function save(): Aggregator
    {
        $this->assertArticles();

        (new SyncArticlesAction)->handle($this->articles->toArray());

        return $this;
    }

    private function assertArticles(): void
    {
        if ($this->articles->isEmpty()) {
            throw new NewsSourceException('No articles to save');
        }
    }
}
