<?php

namespace App\Services\News;

class NewsAggregator
{
    private Sourcable $dataSource;

    public function setDataSource(Sourcable $dataSource): void
    {
        $this->dataSource = $dataSource;
    }

    public function collect()
    {
        $this->dataSource->fetch();
    }
}
