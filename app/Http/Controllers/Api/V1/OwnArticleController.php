<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ArticleResource;
use App\Models\Article;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OwnArticleController extends Controller
{
    /**
     * Fetch preferred articles of a logged-in user
     *
     * @response array{
     * data: ArticleResource[],
     * links: array{
     * "first": "http://localhost/api/v1/own-articles?page=1",
     * "last": null,
     * "prev": null,
     * "next": "http://localhost/api/v1/own-articles?page=2"
     * },
     * meta: array{
     * "current_page": 1,
     * "current_page_url": "http://localhost/api/v1/own-articles?page=1",
     * "from": 1,
     * "path": "http://localhost/api/v1/own-articles",
     * "per_page": 15,
     * "to": 15
     * }}
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    #[QueryParameter(name: 'source', description: 'The source from which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'author', description: 'The author from which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'category', description: 'The category from which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'from_date', description: 'The starting date since which the articles should be fetched', type: 'string')]
    #[QueryParameter(name: 'to_date', description: 'The ending date till which the articles should be fetched', type: 'string')]
    public function __invoke(Request $request)
    {
        $cacheKey = 'preferred_articles';

        if ($request->query()) {
            $queryParams = implode('|', $request->query());
            $cacheKey .= ':' . md5($queryParams);
        }

        $articles = Cache::remember($cacheKey, now()->addHour(), function () use ($request) {
            $query = Article::query();

            // Stick to preferred only, if filters are not provided
            if (!$request->source && !$request->category && !$request->author) {
                $query = Article::preferred($query, $request->user()->preference);
            }

            // Override the preference with filters
            if ($request->source) {
                $query->where('source', $request->source);
            }

            if ($request->category) {
                $query->where('category', $request->category);
            }

            if ($request->author) {
                $query->where('author', $request->author);
            }

            if ($request->from_date) {
                $query->where('published_at', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->where('published_at', '<=', $request->to_date);
            }

            if ($request->keyword) {
                $query->where('title', 'like', "%{$request->keyword}%");
            }

            return $query->orderByDesc('published_at')
                ->simplePaginate();
        });

        return ArticleResource::collection($articles)->additional([
            'message' => 'Articles retrieved!',
        ]);
    }
}
