<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ArticleController
{
    public function show(Request $request, Article $article)
    {
        return response()->json(['data' => $article, 'message' => 'Article Retrieved!']);
    }

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
            $queryParams = implode('|', $request->query());
            $cacheKey .= ':' . md5($queryParams);
        }

        $articles = Cache::remember($cacheKey, now()->addHour(), function () use ($request) {
            return Article::query()
                ->when($request->keyword, fn($query, $keyword) => $query->where('title', 'like', "%{$keyword}%"))
                ->when($request->source, fn($query, $source) => $query->where('source', $source))
                ->when($request->category, fn($query, $category) => $query->where('category', $category))
                ->when($request->author, fn($query, $author) => $query->where('author', $author))
                ->when($request->from_date, fn($query, $fromDate) => $query->where('published_at', '>=', $fromDate))
                ->when($request->to_date, fn($query, $toDate) => $query->where('published_at', '<=', $toDate))
                ->orderByDesc('published_at')->simplePaginate();
        });

        return ArticleResource::collection($articles)->additional([
            'message' => 'Articles retrieved!',
        ]);
    }
}
