<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ArticleResource;
use App\Models\Article;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

final class ArticleController
{
    /**
     * Fetch details for single article.
     *
     * @unauthenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Article $article)
    {
        return response()->json(['data' => $article, 'message' => 'Article Retrieved!']);
    }

    /**
     * Fetch articles for a guest user.
     *
     * @unauthenticated
     *
     * @response array{
     * data: ArticleResource[],
     * links: array{
     * "first": "http://localhost/api/v1/articles?page=1",
     * "last": null,
     * "prev": null,
     * "next": "http://localhost/api/v1/articles?page=2"
     * },
     * meta: array{
     * "current_page": 1,
     * "current_page_url": "http://localhost/api/v1/articles?page=1",
     * "from": 1,
     * "path": "http://localhost/api/v1/articles",
     * "per_page": 15,
     * "to": 15
     * }}
     */
    #[QueryParameter(name: 'source', description: 'The source from which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'author', description: 'The author from which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'category', description: 'The category from which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'from_date', description: 'The starting date since which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'to_date', description: 'The ending date till which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'page', description: 'The page number to load the articles from', type: 'integer')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'articles';

        if ($request->query()) {
            $queryParams = implode('|', $request->query());
            $cacheKey .= ':'.md5($queryParams);
        }

        $articles = Cache::remember($cacheKey, now()->addHour(), function () use ($request) {
            return Article::query()
                ->when($request->keyword, fn ($query, $keyword) => $query->where('title', 'like', "%{$keyword}%"))
                ->when($request->source, fn ($query, $source) => $query->where('source', $source))
                ->when($request->category, fn ($query, $category) => $query->where('category', $category))
                ->when($request->author, fn ($query, $author) => $query->where('author', $author))
                ->when($request->from_date, fn ($query, $fromDate) => $query->where('published_at', '>=', $fromDate))
                ->when($request->to_date, fn ($query, $toDate) => $query->where('published_at', '<=', $toDate))
                ->orderByDesc('published_at')->simplePaginate();
        });

        return ArticleResource::collection($articles)->additional([
            'message' => 'Articles retrieved!',
        ]);
    }
}
