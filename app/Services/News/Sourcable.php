<?php

namespace App\Services\News;

use Illuminate\Support\Collection;

interface Sourcable
{
    public function search(?string $query): NewsSource;

    public function normalize(): NewsSource;

    public function save(): NewsSource;

    public function getArticles(): Collection;
}
