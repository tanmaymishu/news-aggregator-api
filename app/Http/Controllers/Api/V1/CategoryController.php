<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * List all the categories.
     *
     * @unauthenticated
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => Cache::remember('categories', now()->addHour(), function () {
                return Category::latest()->get();
            }),
            'message' => 'Fetched all categories',
        ]);
    }
}
