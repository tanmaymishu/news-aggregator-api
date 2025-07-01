<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OwnArticleController extends Controller
{
    public function __invoke(Request $request)
    {
        $cacheKey = 'preferred_articles';

        if ($request->query()) {
            $queryParams = implode('|', $request->query());
            $cacheKey .= ':'.md5($queryParams);
        }

        $articles = Cache::remember($cacheKey, now()->addHour(), function () use ($request) {
            $query = Article::query();

            return Article::preferred($query, $request->user()->preference)
                ->orderByDesc('published_at')
                ->simplePaginate();
        });

        return ArticleResource::collection($articles)->additional([
            'message' => 'Articles retrieved!',
        ]);
    }
}
