<?php

declare(strict_types=1);

namespace App\Services\News;

use Illuminate\Support\Collection;

interface Sourcable
{
    public function search(?string $query): NewsSource;

    public function normalize(): Collection;
}
