<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class AuthorController
{
    /**
     * List all the authors.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => Cache::remember('authors', now()->addHour(), function () {
                return Author::latest()->get();
            }),
            'message' => 'Fetched all authors',
        ]);
    }
}
