<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ArticleController
{
    /**
     * Fetch articles for an authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'articles';

        if ($request->query()) {
            $query = implode('|', $request->query());
            $cacheKey .= ':'.md5($query);
        }

        $articles = Cache::remember($cacheKey, now()->addHour(), function () use ($request) {
            return Article::query()
                ->when($request->keyword, fn ($query, $keyword) => $query->where('title', 'like', "%{$keyword}%"))
                ->when($request->source, fn ($query, $source) => $query->where('source', $source))
                ->when($request->category, fn ($query, $category) => $query->where('category', $category))
                ->when($request->author, fn ($query, $author) => $query->where('author', $author))
                ->when(
                    $request->from_date,
                    fn ($query, $fromDate) => $query
                        ->whereBetween('published_at', [$request->from_date, $request->to_date ?? today()->subDay()])
                )->when(
                    $request->to_date,
                    fn ($query, $fromDate) => $query
                        ->whereBetween('published_at', [$request->from_date ?? today()->subDay(), $request->to_date])
                )->orderBy('published_at', 'desc')->simplePaginate();
        });

        return ArticleResource::collection($articles)->additional([
            'message' => 'Articles retrieved!',
        ]);
    }
}
