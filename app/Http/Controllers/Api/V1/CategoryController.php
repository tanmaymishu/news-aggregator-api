<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class CategoryController
{
    /**
     * List all the categories.
     *
     * @unauthenticated
     *
     * @response array{
     *  data: Category[],
     *  message: string
     *  }
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
