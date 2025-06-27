<?php

namespace App\News;

interface Sourcable
{
    public function search(?string $query): NewsSource;

    public function normalize(): NewsSource;

    public function save(): NewsSource;
}
